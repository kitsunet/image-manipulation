<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\ImagineInterface;
use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\DescriptionMappingServiceInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageManipulator;
use Kitsunet\ImageManipulation\ImageBlob\ImageServiceInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
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
     * @Flow\Inject
     * @var DescriptionMappingServiceInterface
     */
    protected $descriptionMappingService;

    /**
     * @var ImagineInterface
     */
    protected $imagineService;


    /**
     * @var ImageManipulator
     */
    protected $imageManipulator;

    /**
     * @var array
     */
    protected $imageOptions = [];

    /**
     * @param ImagineInterface $imagineService
     */
    public function injectImagineService(ImagineInterface $imagineService)
    {
        $this->imagineService = $imagineService;
    }

    /**
     * @param ImageManipulator $imageManipulator
     */
    public function injectImageManipulator(ImageManipulator $imageManipulator)
    {
        $this->imageManipulator = $imageManipulator;
    }

    /**
     * @param array $imageOptions
     */
    public function injectImageOptions(array $imageOptions)
    {
        $this->imageOptions = $imageOptions;
    }

    /**
     * @param PersistentResource $originalResource
     * @param ManipulationDescriptionInterface[] $manipulationDescriptions
     * @return array resource, width, height as keys
     * @throws InvalidConfigurationException
     * @throws Exception
     */
    public function processResource(PersistentResource $originalResource, array $manipulationDescriptions)
    {
        $blobMetadata = $this->imageManipulator->prepareMetadata(['options' => $this->getOptionsMergedWithDefaults()], $originalResource);
        $blob = ImagineImageBlob::fromStream($originalResource->getStream(), $blobMetadata);

        $newImageBlob =  $this->process($blob, $manipulationDescriptions);
        $newResource = $this->imageManipulator->storeImageBlob($blob);
        return $this->prepareReturnValue($newResource, $newImageBlob->getSize());
    }

    /**
     * @param ImageBlobInterface $blob
     * @param ManipulationDescriptionInterface[] $manipulationDescriptions
     * @return ImageBlobInterface
     */
    public function process(ImageBlobInterface $blob, array $manipulationDescriptions)
    {
        $manipulations = $this->descriptionMappingService->mapDescriptionsToManipulations($manipulationDescriptions, $blob);
        return $this->manipulate($blob, $manipulations);
    }

    /**
     * @param ImageBlobInterface $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function manipulate(ImageBlobInterface $blob, array $manipulations)
    {
        // TODO: Special handling for SVG should be refactored at a later point.
        if ($blob->getMetadata()->getProperty('mediaType') === 'image/svg+xml') {
            return $blob;
        }

        if ($this->shouldHandleAnimatedGif($blob)) {
            $blob = $this->processAnimatedGif($blob, $manipulations);
        } else {
            $manipulations = $this->imageManipulator->wrapManipulations($blob, $manipulations);
            $blob = $this->imageManipulator->applyManipulationsToBlob($blob, $manipulations);
        }

        return $blob;
    }

    /**
     * @param ImagineImageBlob $blob
     * @return bool
     */
    protected function shouldHandleAnimatedGif(ImagineImageBlob $blob)
    {
        if (!$this->imagineService instanceof \Imagine\Imagick\Imagine) {
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
        $layers = $imagineImage->layers();
        $layers->coalesce();
        $newLayers = [];
        foreach ($layers as $index => $imagineFrame) {
            $layerBlob = ImagineImageBlob::fromImagineImage($imagineFrame, $metadata);
            /** @var ImagineImageBlob $layerBlob */
            $wrappedManipulations = $this->imageManipulator->wrapManipulations($layerBlob, $manipulations);
            $layerBlob = $this->imageManipulator->applyManipulationsToBlob($layerBlob, $wrappedManipulations);
            $newLayers[] = $layerBlob->getImagineImage();
        }

        $imagineImage = array_shift($newLayers);
        $layers = $imagineImage->layers();
        foreach ($newLayers as $imagineFrame) {
            $layers->add($imagineFrame);
        }

        $metadataArray = $metadata->toArray();
        $metadataArray['options']['animated'] = true;
        $metadataArray['options']['animated.delay'] = $imagineImage->getImagick()->getImageDelay() * 10;
        $metadataArray['options']['animated.loops'] = $imagineImage->getImagick()->getImageIterations();

        return ImagineImageBlob::fromImagineImage($imagineImage, new BlobMetadata($metadataArray));
    }

    /**
     * @param array $additionalOptions
     * @return array
     * @throws InvalidConfigurationException
     */
    protected function getOptionsMergedWithDefaults(array $additionalOptions = [])
    {
        $defaultOptions = Arrays::arrayMergeRecursiveOverrule($this->imageOptions, $additionalOptions);
        $quality = isset($defaultOptions['quality']) ? (integer)$defaultOptions['quality'] : 90;
        if ($quality < 0 || $quality > 100) {
            throw new InvalidConfigurationException(
                sprintf('Setting "Neos.Media.image.defaultOptions.quality" allow only value between 0 and 100, current value: %s', $quality),
                1404982574
            );
        }
        $defaultOptions['jpeg_quality'] = $quality;
        // png_compression_level should be an integer between 0 and 9 and inverse to the quality level given. So quality 100 should result in compression 0.
        $defaultOptions['png_compression_level'] = (9 - ceil($quality * 9 / 100));

        return $defaultOptions;
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
