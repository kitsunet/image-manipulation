<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
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
        $imagine = $this->getImagineImage($image);
        $imagine = $imagine->rotate($this->angle);
        return ImagineImageBlob::fromImagineImage($imagine, $image->getMetadata());
    }

    /**
     * @param ManipulationDescriptionInterface $description
     * @return static
     */
    public static function fromDescription(ManipulationDescriptionInterface $description)
    {
        $options = $description->getOptions();
        return new static((int)round($options['angle']));
    }
}
