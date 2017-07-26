<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

/**
 *
 */
class PassthroughDescription implements ManipulationDescriptionInterface
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return ManipulationDescriptionInterface::TYPE_PASSTHROUGH;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [];
    }

}
