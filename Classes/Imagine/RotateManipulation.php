<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 *
 */
class RotateManipulation implements ImageManipulationInterface
{
    use ManipulationHelperTrait;

    /**
     * @var int
     */
    protected $angle;

    /**
     * RotateManipulation constructor.
     *
     * @param int $angle
     */
    public function __construct(int $angle)
    {
        $this->angle = $angle;
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $image = $this->upgradeToImagineBlob($image);

        $imagine = $image->getImagineImage();
        $imagine = $imagine->rotate($this->angle);
        return ImagineImageBlob::fromImagineImage($imagine, $image->getMetadata());
    }

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options)
    {
        if (!isset($options['angle'])) {
            throw new \InvalidArgumentException(sprintf('The "%s" requires an "angle" option to be set to an integer.', static::class), 1501668628932);
        }
        $angle = (int)round((float)$options['angle']);

        /* TODO: Could also allow background image, but needs a ColorInterface of imagine,
           to decide how to provide the information for that. */

        return new static($angle);
    }
}
