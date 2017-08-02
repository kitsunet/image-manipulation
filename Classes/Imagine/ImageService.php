<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\DescriptionMappingServiceInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\PersistentResourceHelper;
use Kitsunet\ImageManipulation\ImageBlob\ImageServiceInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Kitsunet\ImageManipulation\ImageBlob\PassthroughImageManipulation;
use Kitsunet\ImageManipulation\ImageBlob\ResourceProcessorInterface;
use Neos\Flow\ResourceManagement\Exception;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\Exception\InvalidConfigurationException;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Utility\Arrays;

/**
 * @Flow\Scope("singleton")
 */
class ImageService implements ImageServiceInterface, ResourceProcessorInterface
{
    /**
     * @var PersistentResourceHelper
     */
    protected $persistentResourceHelper;

    /**
     * @var ImageBlobFactory
     */
    protected $imageBlobFactory;

    /**
     * @param ImageBlobFactory $imageBlobFactory
     */
    public function injectImageBlobFactory(ImageBlobFactory $imageBlobFactory)
    {
        $this->imageBlobFactory = $imageBlobFactory;
    }

    /**
     * @param PersistentResourceHelper $persistentResourceHelper
     */
    public function injectPersistentResourceHelper(PersistentResourceHelper $persistentResourceHelper)
    {
        $this->persistentResourceHelper = $persistentResourceHelper;
    }

    /**
     * @param PersistentResource $originalResource
     * @param ImageManipulationInterface[] $manipulations
     * @return array resource, width, height as keys
     * @throws InvalidConfigurationException
     * @throws Exception
     */
    public function processResource(PersistentResource $originalResource, array $manipulations)
    {
        $blobMetadata = $this->persistentResourceHelper->prepareMetadata($originalResource);
        $blob = $this->imageBlobFactory->create($originalResource->getStream(), $blobMetadata);

        $blob = $this->manipulate($blob, $manipulations);
        $newResource = $this->persistentResourceHelper->storeImageBlob($blob, $originalResource->getCollectionName());
        return $this->prepareReturnValue($newResource, $blob->getSize());
    }

    /**
     * @param ImageBlobInterface $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function manipulate(ImageBlobInterface $blob, array $manipulations)
    {
        // TODO: Special handling for SVG should be refactored at a later point.
        if ($blob->getMetadata()->getMediaType() === 'image/svg+xml') {
            return $blob;
        }

        if ($this->shouldHandleAnimatedGif($blob)) {
            $blob = $this->processAnimatedGif($blob, $manipulations);
        } else {
            $manipulations = $this->persistentResourceHelper->wrapManipulations($blob, $manipulations);
            $blob = $manipulations->applyTo($blob);
        }

        return $blob;
    }

    /**
     * @param ImagineImageBlob $blob
     * @return bool
     */
    protected function shouldHandleAnimatedGif(ImagineImageBlob $blob)
    {
        if (!($blob->getImagineImage() instanceof \Imagine\Imagick\Image)) {
            return false;
        }

        /** @var \Imagine\Imagick\Image $imagineImage */
        $imagineImage = $blob->getImagineImage();
        $imagick = $imagineImage->getImagick();

        if ($imagick->getImageMimeType() !== 'image/gif') {
            return false;
        }

        if ($imagick->getImageDelay() === 0) {
            return false;
        }

        return true;
    }

    /**
     * @param ImagineImageBlob $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImagineImageBlob
     */
    protected function processAnimatedGif(ImagineImageBlob $blob, array $manipulations)
    {
        /** @var \Imagine\Imagick\Image $imagineImage */
        $imagineImage = $blob->getImagineImage();
        $metadata = $blob->getMetadata();

        $newMetadata = $metadata->withOptions(Arrays::arrayMergeRecursiveOverrule($metadata->getOptions(), [
            'imagine' => [
                'animated' => true,
                'animated.delay' => $imagineImage->getImagick()->getImageDelay() * 10,
                'animated.loops' => $imagineImage->getImagick()->getImageIterations()
            ]
        ]));

        $layers = $imagineImage->layers();
        $layers->coalesce();
        $newLayers = [];
        foreach ($layers as $index => $imagineFrame) {
            $layerBlob = ImagineImageBlob::fromImagineImage($imagineFrame, $newMetadata);
            /** @var ImagineImageBlob $layerBlob */
            $wrappedManipulations = $this->persistentResourceHelper->wrapManipulations($layerBlob, $manipulations);
            $layerBlob = $wrappedManipulations->applyTo($layerBlob);
            $newLayers[] = $layerBlob->getImagineImage();
        }

        $imagineImage = array_shift($newLayers);
        $layers = $imagineImage->layers();
        foreach ($newLayers as $imagineFrame) {
            $layers->add($imagineFrame);
        }

        $blob = ImagineImageBlob::fromImagineImage($imagineImage, $newMetadata);
        return $blob;
    }

    /**
     * Prepare an array to return.
     *
     * @param PersistentResource $resource
     * @param BoxInterface $imageSize
     * @return array
     */
    protected function prepareReturnValue(PersistentResource $resource, BoxInterface $imageSize)
    {
        return [
            'width' => $imageSize->getWidth(),
            'height' => $imageSize->getHeight(),
            'resource' => $resource
        ];
    }
}
