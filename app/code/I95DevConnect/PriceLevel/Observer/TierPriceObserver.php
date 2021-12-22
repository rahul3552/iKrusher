<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Observer;

use Magento\Framework\Event\ObserverInterface;
use I95DevConnect\PriceLevel\Helper\Data;

/**
 * Observer to make tier prices form read only
 */
class TierPriceObserver implements ObserverInterface
{

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $data;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param Data $data
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        Data $data
    ) {

        $this->logger = $logger;
        $this->data = $data;
        $this->dataHelper = $dataHelper;
    }

    /**
     * set read only tier prices ui to product
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        if (!$this->data->isEnabled()) {
            return $this;
        }
        if (!isset($block)) {
            return $this;
        }

        $blockClass = get_class_methods($block);

        if (\Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Tier::class == $blockClass) {
            $block->setTemplate('I95DevConnect_PriceLevel::tier.phtml');
        }
    }
}
