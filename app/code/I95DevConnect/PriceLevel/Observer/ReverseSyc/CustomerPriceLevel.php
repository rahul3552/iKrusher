<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Observer\ReverseSyc;

use Magento\Framework\Event\ObserverInterface;
use I95DevConnect\PriceLevel\Helper\PriceLevel as PriceLevelHelper;

/**
 * Observer to assign Price Level to the Customer from ERP
 */
class CustomerPriceLevel implements ObserverInterface
{

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory
     */
    public $magentoPriceLevelFactory;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $helper;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Helper\PriceLevel
     */
    public $priceLevelHelper;
    
    /**
     * Class constructor to include all the dependencies
     *
     * @param \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $magentoPriceLevelFactory
     * @param \I95DevConnect\PriceLevel\Helper\Data $helper
     * @param PriceLevelHelper $helper
     */
    public function __construct(
        \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $magentoPriceLevelFactory,
        \I95DevConnect\PriceLevel\Helper\Data $helper,
        PriceLevelHelper $priceLevelHelper
    ) {
        $this->magentoPriceLevelFactory = $magentoPriceLevelFactory;
        $this->helper = $helper;
        $this->priceLevelHelper = $priceLevelHelper;
    }

    /**
     * Assign Price Level to the Customer
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currentObject = $observer->getEvent()->getData("currentObject");
        $erpPriceLevel = $currentObject->dataHelper
                ->getValueFromArray("priceLevel", $currentObject->stringData);
        if (isset($erpPriceLevel) && $erpPriceLevel != '' && $this->helper->isEnabled()) {
            $this->priceLevelHelper->validatePricelevel($erpPriceLevel);
            $currentObject->customerInterface->setCustomAttribute('pricelevel', $erpPriceLevel);
        } else {
            $currentObject->customerInterface->setCustomAttribute('pricelevel', null);
        }
    }
}
