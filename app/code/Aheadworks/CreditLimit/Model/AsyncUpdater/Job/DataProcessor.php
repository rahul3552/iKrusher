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

use Aheadworks\CreditLimit\Api\Data\JobInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class DataProcessor
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job
 */
class DataProcessor
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param JsonSerializer $serializer
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        JsonSerializer $serializer
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->serializer = $serializer;
    }

    /**
     * Convert to data array for saving
     *
     * @param JobInterface $jobDataObject
     * @return array
     */
    public function processBeforeSave($jobDataObject)
    {
        $jobData = $this->dataObjectProcessor->buildOutputDataArray(
            $jobDataObject,
            JobInterface::class
        );

        return $jobData;
    }

    /**
     * Prepare data to process after load
     *
     * @param array $jobArrayData
     * @return array
     */
    public function processAfterLoad($jobArrayData)
    {
        $jobArrayData[JobInterface::CONFIGURATION]
            = $this->serializer->unserialize($jobArrayData[JobInterface::CONFIGURATION]);

        return $jobArrayData;
    }
}
