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
     * @var SystemLoggerInterface
     */
    protected $systemLogger;

    /**
     * @var ManipulationDescriptionInterface
     */
    protected $description;

    /**
     * LoggingPassthroughImageManipulation constructor.
     *
     * @param ManipulationDescriptionInterface $description
     */
    public function __construct(ManipulationDescriptionInterface $description)
    {
        $this->description = $description;
    }

    /**
     * @param SystemLoggerInterface $systemLogger
     */
    public function injectSystemLogger(SystemLoggerInterface $systemLogger)
    {
        $this->systemLogger = $systemLogger;
    }

    /**
     * @param ManipulationDescriptionInterface $description
     * @return static
     */
    public static function fromDescription(ManipulationDescriptionInterface $description): LoggingPassthroughImageManipulation
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
