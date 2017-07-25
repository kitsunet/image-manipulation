<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\SystemLoggerInterface;

/**
 *
 */
class LoggingPassthroughImageManipulation implements ImageManipulationInterface
{
    /**
     * @Flow\Inject
     * @var SystemLoggerInterface
     */
    protected $systemLogger;

    /**
     * @var ManipulationDescriptionInterface
     */
    protected $description;

    public function __construct(ManipulationDescriptionInterface $description)
    {
        $this->description = $description;
    }

    /**
     * @param ManipulationDescriptionInterface $description
     * @return static
     */
    public static function fromDescription(ManipulationDescriptionInterface $description)
    {
        return new static();
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $this->systemLogger->log(
            sprintf('LoggingPassthroughImageManipulation was applied to blob of class "%s" and previously created with description of class "%s"', get_class($image), get_class($this->description)),
            LOG_DEBUG,
            null,
            'ImageBlob'
        );

        return $image;
    }
}
