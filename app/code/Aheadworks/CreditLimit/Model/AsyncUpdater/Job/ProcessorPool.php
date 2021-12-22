<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\AsyncUpdater\Job;

/**
 * Class ProcessorPool
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job
 */
class ProcessorPool
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(
        $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Get processor
     *
     * @param string $jobType
     * @return ProcessorInterface
     */
    public function getProcessor($jobType)
    {
        if (isset($this->processors[$jobType])) {
            if (!$this->processors[$jobType] instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Job processor does not implement required interface: %s.',
                        ProcessorInterface::class
                    )
                );
            }
            return $this->processors[$jobType];
        }

        throw new \InvalidArgumentException(
            sprintf('Job processor is not found for job type: %s.', $jobType)
        );
    }
}
