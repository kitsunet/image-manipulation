<?php
namespace Kitsunet\ImageManipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 * A decomposable manipulation description is so complex that it needs the
 * image blob it is to be applied to, to dertermine it's actual components.
 */
interface DecomposableInterface
{
    /**
     * This will be called on a decomposable description by any mapper.
     *
     * It should return an array of \Kitsunet\ImageManipulation\Description\ManipulationDescriptionInterface
     * that can then be turned into actual operations.
     *
     * @param ImageBlobInterface $imageBlob
     * @return ManipulationDescriptionInterface[]
     */
    public function decompose(ImageBlobInterface $imageBlob): array;
}
