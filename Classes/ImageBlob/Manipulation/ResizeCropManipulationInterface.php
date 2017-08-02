<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

/**
 * Crop
 *
 */
interface ResizeCropManipulationInterface
{
    /**
     * Inset ratio mode: If an image is attempted to get scaled with the size of both edges stated, using this mode will scale it to the lower of both edges.
     * Consider an image of 320/480 being scaled to 50/50: because aspect ratio wouldn't get hurt, the target image size will become 33/50.
     */
    const RATIOMODE_INSET = 'inset';

    /**
     * Outbound ratio mode: If an image is attempted to get scaled with the size of both edges stated, using this mode will scale the image and crop it.
     * Consider an image of 320/480 being scaled to 50/50: the image will be scaled to height 50, then centered and cropped so the width will also be 50.
     */
    const RATIOMODE_OUTBOUND = 'outbound';
}
