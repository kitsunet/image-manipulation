<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Utility\Unicode\Functions as UnicodeFunctions;

/**
 *
 */
class ImageManipulator
{
    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var DescriptionMappingServiceInterface
     */
    protected $descriptionMappingService;

    /**
     * @var array
     */
    protected $preDescriptions = [];

    /**
     * @var array
     */
    protected $postDescriptions = [];

    /**
     * @param array $preDescriptions
     */
    public function injectPreDescriptions(array $preDescriptions)
    {
        $this->preDescriptions = ConfigurationToDescriptionMapper::mapDescriptionConfiguration($preDescriptions);
    }

    /**
     * @param array $postDescriptions
     */
    public function injectPostDescriptions(array $postDescriptions)
    {
        $this->postDescriptions = ConfigurationToDescriptionMapper::mapDescriptionConfiguration($postDescriptions);
    }

    /**
     * ImageManipulator constructor.
     *
     * @param ResourceManager $resourceManager
     * @param DescriptionMappingServiceInterface $descriptionMappingService
     */
    public function __construct(ResourceManager $resourceManager, DescriptionMappingServiceInterface $descriptionMappingService)
    {
        $this->resourceManager = $resourceManager;
        $this->descriptionMappingService = $descriptionMappingService;
    }

    /**
     * @param array $initialMetadata
     * @param PersistentResource $originalResource
     * @return BlobMetadata
     */
    public function prepareMetadata(array $initialMetadata, PersistentResource $originalResource): BlobMetadata
    {
        $metadataArray = array_merge($initialMetadata, [
            'collection' => $originalResource->getCollectionName(),
            'filename' => $originalResource->getFilename(),
            'mediaType' => $originalResource->getMediaType(),
            'fileExtension' => $originalResource->getFileExtension(),
        ]);
        return new BlobMetadata($metadataArray);
    }

    /**
     * Store the blob as resource using it's metadata to generate a filename.
     *
     * @param ImageBlobInterface $blob
     * @param string $filename
     * @param string $collectionName
     * @return PersistentResource
     * @throws ImageFileException
     */
    public function storeImageBlob(ImageBlobInterface $blob, string $filename = null, string $collectionName = null): PersistentResource
    {
        $collectionName = $collectionName ?? $blob->getMetadata()->getProperty('collection');
        $resource = $this->resourceManager->importResource($blob->getStream(), $collectionName);
        if ($resource === false) {
            throw new ImageFileException('An error occurred while importing a generated image file as a resource.', 1413562208);
        }

        $imageSize = $blob->getSize();
        $pathInfo = UnicodeFunctions::pathinfo($blob->getMetadata()->getProperty('filename'));
        $filename = $filename ?? sprintf('%s-%ux%u.%s', $pathInfo['filename'], $imageSize->getWidth(), $imageSize->getHeight(), $pathInfo['extension']);
        $resource->setFilename($filename);
        return $resource;
    }

    /**
     * Adds (configured) pre and post manipulations to the stack.
     *
     * @param ImageBlobInterface $imageBlob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageManipulationInterface[]
     */
    public function wrapManipulations(ImageBlobInterface $imageBlob, array $manipulations): array
    {
        $preManipulations = $this->descriptionMappingService->mapDescriptionsToManipulations($this->preDescriptions, $imageBlob);
        $postManipulations = $this->descriptionMappingService->mapDescriptionsToManipulations($this->postDescriptions, $imageBlob);
        return array_merge($preManipulations, $manipulations, $postManipulations);
    }

    /**
     * Applies a stack of manipulations to an image.
     *
     * @param ImageBlobInterface $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface
     */
    public function applyManipulationsToBlob(ImageBlobInterface $blob, array $manipulations): ImageBlobInterface
    {
        /** @var ImageBlobInterface $blob */
        return array_reduce($manipulations, function (ImageBlobInterface $blob, ImageManipulationInterface $manipulation) {
            return $manipulation->applyTo($blob);
        }, $blob);
    }
}
