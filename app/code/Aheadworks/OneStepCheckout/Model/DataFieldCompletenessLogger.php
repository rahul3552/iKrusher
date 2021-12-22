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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\DataFieldCompletenessLoggerInterface;
use Aheadworks\OneStepCheckout\Api\Data\DataFieldCompletenessInterface;
use Aheadworks\OneStepCheckout\Model\ResourceModel\FieldCompleteness\Logger;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class DataFieldCompletenessLogger
 * @package Aheadworks\OneStepCheckout\Model
 */
class DataFieldCompletenessLogger implements DataFieldCompletenessLoggerInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param Logger $logger
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        Logger $logger,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->logger = $logger;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function log($cartId, array $fieldCompleteness)
    {
        $logData = [];
        foreach ($fieldCompleteness as $item) {
            $logData[] = $this->dataObjectProcessor->buildOutputDataArray(
                $item,
                DataFieldCompletenessInterface::class
            );
        }
        $this->logger->log($cartId, $logData);
    }
}
