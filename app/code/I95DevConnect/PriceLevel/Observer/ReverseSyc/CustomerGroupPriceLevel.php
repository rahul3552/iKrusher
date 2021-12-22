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
 * Observer to assign Price Level to the Customer Group from ERP
 */
class CustomerGroupPriceLevel implements ObserverInterface
{

    public $currentObject;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory
     */
    public $magentoPriceLevelFactory;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\CustomerGroupFactory
     */
    public $modelCustomerGroup;

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
     * @param \I95DevConnect\MessageQueue\Model\CustomerGroupFactory $modelCustomerGroup
     * @param \I95DevConnect\PriceLevel\Helper\Data $helper
     * @param PriceLevelHelper $helper
     */
    public function __construct(
        \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $magentoPriceLevelFactory,
        \I95DevConnect\MessageQueue\Model\CustomerGroupFactory $modelCustomerGroup,
        \I95DevConnect\PriceLevel\Helper\Data $helper,
        PriceLevelHelper $priceLevelHelper
    ) {
        $this->magentoPriceLevelFactory = $magentoPriceLevelFactory;
        $this->modelCustomerGroup = $modelCustomerGroup;
        $this->helper = $helper;
        $this->priceLevelHelper = $priceLevelHelper;
    }

    /**
     * Assign Price Level to the Customer Group
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->currentObject = $observer->getEvent()->getData("currentObject");
        $component = $this->currentObject->dataHelper->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        if ($component != "NAV") {
            $erpPriceLevel = $this->currentObject->dataHelper
                    ->getValueFromArray("priceLevel", $this->currentObject->stringData);
            $targetCustomerGroupId = $this->currentObject->dataHelper
                    ->getValueFromArray("customerGroup", $this->currentObject->stringData);
            /** @updatedBy Debashis S. Gopal. Fetching $customerGroupId from object as it is changed
             * to object type in customer group.
             */
            $customerGroupId = $this->currentObject->resultData->getId();
            if (isset($erpPriceLevel) && $erpPriceLevel != '' && $this->helper->isEnabled()) {
                $pricelevelData = $this->priceLevelHelper->validatePricelevel($erpPriceLevel);
                $priceLevelId = $pricelevelData[0]['pricelevel_id'];
                if ($customerGroupId == "") {
                    $customGroup = $this->modelCustomerGroup->create();
                } else {
                    $modelCustomerGroupData = $this->modelCustomerGroup->create()
                            ->getCollection()
                            ->addFieldToFilter('customer_group_id', $customerGroupId)
                            ->getData();
                    $id = $modelCustomerGroupData[0]['id'];
                    $customGroup = $this->modelCustomerGroup->create()->load($id);
                }
                $customGroup->setcustomerGroupId($customerGroupId);
                $customGroup->settargetGroupId($targetCustomerGroupId);
                $customGroup->setpricelevelId($priceLevelId);
                $customGroup->setcreatedAt($this->currentObject->date->gmtDate());
                $customGroup->setupdatedAt($this->currentObject->date->gmtDate());
                $customGroup->setupdateBy('ERP');
                $customGroup->save();
            }
        }
    }
}
