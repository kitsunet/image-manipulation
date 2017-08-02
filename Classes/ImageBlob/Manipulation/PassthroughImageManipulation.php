<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 * A "NULL" operation manipulation that does nothing and just passes the incoming imageblob.
 */
class PassthroughImageManipulation implements ImageManipulationInterface
{
    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options)
    {
        return new static();
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        return $image;
    }
}
