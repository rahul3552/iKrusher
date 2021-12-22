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
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class AmazonVersionPool
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class AmazonVersionPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $versionProcessors = [];

    /**
     * @var StatusInterface[]
     */
    private $processorInstance = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $versionProcessors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $versionProcessors = []
    ) {
        $this->objectManager = $objectManager;
        $this->versionProcessors = $versionProcessors;
    }

    /**
     * Get Amazon version processor according to module version
     *
     * @param $moduleVersion
     * @return StatusInterface
     * @throws \Exception
     */
    public function getAmazonVersionProcessor($moduleVersion)
    {
        if (isset($this->processorInstance[$moduleVersion])) {
            return $this->processorInstance[$moduleVersion];
        }

        if (isset($this->versionProcessors[$moduleVersion])) {
            if ($this->isAmazonVersionProcessorCanBeCreated($this->versionProcessors[$moduleVersion])) {
                $this->processorInstance[$moduleVersion]
                    = $this->objectManager->create($this->versionProcessors[$moduleVersion]);
                return $this->processorInstance[$moduleVersion];
            } else {
                throw new LocalizedException(
                    sprintf('Class not found %s', $this->versionProcessors[$moduleVersion])
                );
            }
        } elseif (!isset($this->processorInstance['default'])) {
            if ($this->isAmazonVersionProcessorCanBeCreated($this->versionProcessors['default'])) {
                $this->processorInstance['default']
                    = $this->objectManager->create($this->versionProcessors['default']);
                return $this->processorInstance['default'];
            }
        } else {
            return $this->processorInstance['default'];
        }
    }

    /**
     * Check if Amazon version processor can be created
     *
     * @param $amazonVersionProcessor
     * @return bool
     */
    private function isAmazonVersionProcessorCanBeCreated($amazonVersionProcessor)
    {
        return class_exists($amazonVersionProcessor);
    }
}
