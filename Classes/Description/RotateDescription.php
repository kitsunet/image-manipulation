<?php
namespace Kitsunet\ImageManipulation\Description;

/**
 * Describes rotation of an image.
 */
class RotateDescription implements ManipulationDescriptionInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * RotateDescription constructor.
     *
     * @param float $angle
     */
    public function __construct(float $angle)
    {
        $this->options = [
            'angle' => $angle
        ];
    }

    public static function createTurnHalf()
    {
        return new static(180.0);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return ManipulationDescriptionInterface::TYPE_ROTATE;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
