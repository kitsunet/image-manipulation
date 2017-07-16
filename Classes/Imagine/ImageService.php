<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Image;
use Imagine\Imagick\Imagine;
use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlob;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageManipulationInterface;
use Kitsunet\ImageManipulation\ImageBlob\PassthroughImageManipulation;
use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\ResourceManagement\Exception;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\Exception\InvalidConfigurationException;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\Utility\Environment;
use Neos\Media\Domain\Model\Adjustment\AdjustmentInterface;
use Neos\Utility\Arrays;
use Neos\Utility\Unicode\Functions as UnicodeFunctions;
use Neos\Media\Domain\Model\Adjustment\CropImageAdjustment;
use Neos\Media\Domain\Model\Adjustment\ResizeImageAdjustment;
use Neos\Media\Exception\ImageFileException;

/**
 * @Flow\Scope("singleton")
 */
class ImageService
{
    /**
     * @var ImagineInterface
     */
    protected $imagineService;

    /**
     * @var ResourceManager
     * @Flow\Inject
     */
    protected $resourceManager;

    /**
     * @Flow\Inject
     * @var Environment
     */
    protected $environment;

    /**
     * @var VariableFrontend
     */
    protected $imageSizeCache;

    /**
     * @Flow\InjectConfiguration(package="Neos.Media")
     * @var array
     */
    protected $settings;

    /**
     * @param ImagineInterface $imagineService
     */
    public function injectImagineService(ImagineInterface $imagineService)
    {
        $this->imagineService = $imagineService;
    }

    /**
     * @param PersistentResource $originalResource
     * @param array $adjustments
     * @return array resource, width, height as keys
     * @throws ImageFileException
     * @throws InvalidConfigurationException
     * @throws Exception
     */
    public function processImage(PersistentResource $originalResource, array $adjustments)
    {
        $manipulations = $this->mapAdjustmentsToManipulations($adjustments);
        return $this->process($originalResource, $manipulations);
    }

    /**
     * @param PersistentResource $originalResource
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface
     * @throws ImageFileException
     */
    public function process(PersistentResource $originalResource, array $manipulations)
    {
        $blobMetadata = $this->prepareMetadata($originalResource);
        // TODO: Special handling for SVG should be refactored at a later point.
        if ($originalResource->getMediaType() === 'image/svg+xml') {
            $blob = ImageBlob::fromStream($originalResource->getStream(), $blobMetadata);
            $newResource = $this->storeModifiedImageBlob($blob);
            return $this->prepareReturnValue($newResource, $blob->getSize());
        }

        $blob = ImagineImageBlob::fromStream($originalResource->getStream(), $blobMetadata);
        if ($this->shouldHandleAnimatedGif($blob)) {
            $blob = $this->processAnimatedGif($blob, $manipulations);
        } else {
            $blob = $this->applyManipulationsToBlob($blob, $manipulations);
        }

        $resource = $this->storeModifiedImageBlob($blob);
        return $this->prepareReturnValue($resource, $blob->getSize());
    }

    /**
     * @param ImagineImageBlob $blob
     * @return bool
     */
    protected function shouldHandleAnimatedGif(ImagineImageBlob $blob)
    {
        if (!$this->imagineService instanceof Imagine) {
            return false;
        }

        /** @var Image $imagineImage */
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
        /** @var Image $imagineImage */
        $imagineImage = $blob->getImagineImage();
        $metadata = $blob->getMetadata();
        $layers = $imagineImage->layers();
        $layers->coalesce();
        $newLayers = [];
        foreach ($layers as $index => $imagineFrame) {
            $layerBlob = ImagineImageBlob::fromImagineImage($imagineFrame, $metadata);
            /** @var ImagineImageBlob $layerBlob */
            $layerBlob = $this->applyManipulationsToBlob($layerBlob, $manipulations);
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
     * @param PersistentResource $originalResource
     * @return BlobMetadata
     */
    protected function prepareMetadata(PersistentResource $originalResource)
    {
        return new BlobMetadata([
            'collection' => $originalResource->getCollectionName(),
            'filename' => $originalResource->getFilename(),
            'mediaType' => $originalResource->getMediaType(),
            'fileExtension' => $originalResource->getFileExtension(),
            'options' => $this->getOptionsMergedWithDefaults()
        ]);
    }

    /**
     * @param PersistentResource $resource
     * @param BoxInterface $imageSize
     * @return array
     */
    protected function prepareReturnValue(PersistentResource $resource, BoxInterface $imageSize)
    {
        // todo: Refactor caching of image sizes...
        $this->imageSizeCache->set($resource->getCacheEntryIdentifier(), [
            'width' => $imageSize->getWidth(),
            'height' => $imageSize->getHeight()
        ]);

        return [
            'width' => $imageSize->getWidth(),
            'height' => $imageSize->getHeight(),
            'resource' => $resource
        ];
    }

    /**
     * @param ImageBlobInterface $blob
     * @return PersistentResource
     * @throws ImageFileException
     */
    protected function storeModifiedImageBlob(ImageBlobInterface $blob)
    {
        $resource = $this->resourceManager->importResource($blob->getStream(), $blob->getMetadata()->getProperty('collection'));
        if ($resource === false) {
            throw new ImageFileException('An error occurred while importing a generated image file as a resource.', 1413562208);
        }

        $imageSize = $blob->getSize();
        $pathInfo = UnicodeFunctions::pathinfo($blob->getMetadata()->getProperty('filename'));
        $resource->setFilename(sprintf('%s-%ux%u.%s', $pathInfo['filename'], $imageSize->getWidth(), $imageSize->getHeight(), $pathInfo['extension']));

        return $resource;
    }

    /**
     * @param ImageBlobInterface $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface
     */
    protected function applyManipulationsToBlob(ImageBlobInterface $blob, array $manipulations)
    {
        /** @var ImageBlobInterface $blob */
        $blob = array_reduce($manipulations, function (ImageBlobInterface $blob, ImageManipulationInterface $manipulation) {
            return $manipulation->applyTo($blob);
        }, $blob);

        $manipulation = new GrayscaleManipulation();
        return $manipulation->applyTo($blob);
    }

    /**
     * @param array $adjustments
     * @return ImageManipulationInterface[]
     */
    protected function mapAdjustmentsToManipulations(array $adjustments)
    {
        // TODO: The translation of adjustments should happen via a simple meta format that is then translated by implementation specific factories
        return array_map([$this, 'convertAdjustmentToManipulation'], $adjustments);
    }

    /**
     * @param AdjustmentInterface $adjustment
     * @return ImageManipulationInterface
     */
    protected function convertAdjustmentToManipulation(AdjustmentInterface $adjustment)
    {
        $manipulation = new PassthroughImageManipulation();
        if ($adjustment instanceof CropImageAdjustment) {
            $manipulation = new CropManipulation($adjustment->getX(), $adjustment->getY(), $adjustment->getWidth(), $adjustment->getHeight());
        }

        if ($adjustment instanceof ResizeImageAdjustment) {
            if ($adjustment->getWidth() && !$adjustment->getHeight()) {
                $manipulation = ResizeManipulation::toWidth($adjustment->getWidth());
            }
            if (!$adjustment->getWidth() && $adjustment->getHeight()) {
                $manipulation = ResizeManipulation::toHeight($adjustment->getHeight());
            }
            if ($adjustment->getWidth() && $adjustment->getHeight()) {
                $manipulation = ResizeManipulation::toDimensions($adjustment->getWidth(), $adjustment->getHeight());
            }
        }

        return $manipulation;
    }

    /**
     * @param array $additionalOptions
     * @return array
     * @throws InvalidConfigurationException
     */
    protected function getOptionsMergedWithDefaults(array $additionalOptions = [])
    {
        $defaultOptions = Arrays::getValueByPath($this->settings, 'image.defaultOptions');
        if (!is_array($defaultOptions)) {
            $defaultOptions = [];
        }
        if ($additionalOptions !== []) {
            $defaultOptions = Arrays::arrayMergeRecursiveOverrule($defaultOptions, $additionalOptions);
        }
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
     * Get the size of a Flow PersistentResource that contains an image file.
     *
     * @param PersistentResource $resource
     * @return array
     * @throws ImageFileException
     */
    public function getImageSize(PersistentResource $resource)
    {
        $cacheIdentifier = $resource->getCacheEntryIdentifier();

        $imageSize = $this->imageSizeCache->get($cacheIdentifier);
        if ($imageSize === false) {
            $imageSize = $this->calculateImageSize($resource->getStream());
            $this->imageSizeCache->set($cacheIdentifier, $imageSize);
        }

        return $imageSize;
    }

    /**
     * @param resource $stream
     * @return array
     * @throws ImageFileException
     */
    protected function calculateImageSize($stream)
    {
        try {
            $imagineImage = $this->imagineService->read($stream);
            $sizeBox = $imagineImage->getSize();
            $imageSize = ['width' => $sizeBox->getWidth(), 'height' => $sizeBox->getHeight()];
        } catch (\Exception $e) {
            throw new ImageFileException(sprintf('The given resource was not an image file your choosen driver can open. The original error was: %s', $e->getMessage()), 1336662898, $e);
        }
        return $imageSize;
    }
}
