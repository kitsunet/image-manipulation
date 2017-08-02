<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\Description\ConfigurationToDescriptionMapper;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Manipulator;
use Neos\Flow\Annotations as Flow;
use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Utility\Arrays;
use Neos\Utility\Unicode\Functions as UnicodeFunctions;

/**
 * Basic functionality to deal with creation, manipulation and storing of PersistentResources in the context of Imageblobs.
 *
 * @Flow\Scope("singleton")
 */
class PersistentResourceHelper
{
    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @Flow\InjectConfiguration(package="Kitsunet.ImageManipulation", path="ImageBlob.metadata.defaultOptions")
     * @var array
     */
    protected $metadataDefaultOptions;

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
     * @param PersistentResource $originalResource
     * @param array $options
     * @return BlobMetadata
     */
    public function prepareMetadata(PersistentResource $originalResource, array $options = []): BlobMetadata
    {
        $options = Arrays::arrayMergeRecursiveOverrule($this->metadataDefaultOptions, $options);
        return new BlobMetadata($originalResource->getFilename(), $originalResource->getMediaType(), $options);
    }

    /**
     * Store the blob as resource using it's metadata to generate a filename.
     *
     * @param ImageBlobInterface $blob
     * @param string $collectionName
     * @param string $filename
     * @return PersistentResource
     * @throws ImageFileException
     */
    public function storeImageBlob(ImageBlobInterface $blob, string $collectionName, string $filename = null): PersistentResource
    {
        $resource = $this->resourceManager->importResource($blob->getStream(), $collectionName);
        if ($resource === false) {
            throw new ImageFileException('An error occurred while importing a generated image file as a resource.', 1413562208);
        }

        $imageSize = $blob->getSize();
        $pathInfo = UnicodeFunctions::pathinfo($blob->getMetadata()->getFilename());
        $filename = $filename ?? sprintf('%s-%ux%u.%s', $pathInfo['filename'], $imageSize->getWidth(), $imageSize->getHeight(), $pathInfo['extension']);
        $resource->setFilename($filename);
        return $resource;
    }

    /**
     * Adds (configured) pre and post manipulations to the stack.
     *
     * @param ImageBlobInterface $imageBlob
     * @param ImageManipulationInterface[] $manipulations
     * @return Manipulator
     */
    public function wrapManipulations(ImageBlobInterface $imageBlob, array $manipulations): Manipulator
    {
        return new Manipulator($manipulations);
        $preManipulations = $this->descriptionMappingService->mapDescriptionsToManipulations($this->preDescriptions, $imageBlob);
        $postManipulations = $this->descriptionMappingService->mapDescriptionsToManipulations($this->postDescriptions, $imageBlob);
        return new Manipulator(array_merge($preManipulations, $manipulations, $postManipulations));
    }
}
