<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

/**
 * Describes a passthrough manipulation that does nothing.
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
