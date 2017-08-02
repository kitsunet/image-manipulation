<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 *
 */
class Manipulator implements ImageManipulationInterface
{
    /**
     * @var array
     */
    protected $manipulations;

    /**
     * Manipulator constructor.
     *
     * @param ImageManipulationInterface[] $manipulations
     */
    public function __construct(array $manipulations)
    {
        $this->manipulations = $manipulations;
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        return $this->applyManipulationsToBlob($image, $this->manipulations);
    }

    /**
     * Applies a stack of manipulations to an image.
     *
     * @param ImageBlobInterface $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface
     */
    protected function applyManipulationsToBlob(ImageBlobInterface $blob, array $manipulations): ImageBlobInterface
    {
        /** @var ImageBlobInterface $blob */
        return array_reduce($manipulations, function (ImageBlobInterface $blob, ImageManipulationInterface $manipulation) {
            return $manipulation->applyTo($blob);
        }, $blob);
    }

    /**
     * Creates the manipulator with an array describing manipulations and their options.
     *
     * @param array $options
     * @return ImageManipulationInterface
     */
    public static function fromOptions(array $options): ImageManipulationInterface
    {
        return new self($options);
    }
}
