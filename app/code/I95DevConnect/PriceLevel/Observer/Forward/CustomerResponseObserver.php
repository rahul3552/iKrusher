<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Observer\Forward;

use \Magento\Framework\Event\ObserverInterface;
use \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory;
use Magento\Framework\App\Request\Http;

/**
 * Observer to assign Price Level to the Customer from ERP
 */
class CustomerResponseObserver implements ObserverInterface
{
    const INPUTDATA = "inputData";
    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory
     */
    public $logger;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     *
     * @var \Magento\Customer\Model\Customer
     */
    public $customerModel;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $helper;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory
     */
    public $magentoPriceLevelFactory;
    /**
     * @var Http
     */
    public $request;

    /**
     * Class constructor to include all the dependencies
     *
     * @param LoggerInterfaceFactory $logger
     * @param \Magento\Customer\Model\CustomerFactory $customerModel
     * @param Http $request
     * @param \I95DevConnect\PriceLevel\Helper\Data $helper
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $magentoPriceLevelFactory
     */
    public function __construct(
        LoggerInterfaceFactory $logger,
        \Magento\Customer\Model\CustomerFactory $customerModel,
        Http $request,
        \I95DevConnect\PriceLevel\Helper\Data $helper,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $magentoPriceLevelFactory
    ) {
        $this->logger = $logger;
        $this->customerModel = $customerModel;
        $this->request = $request;
        $this->helper = $helper;
        $this->dataHelper = $dataHelper;
        $this->magentoPriceLevelFactory = $magentoPriceLevelFactory;
    }

    /**
     * Assign Price Level to the Customer for response sent for forward customer sync
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return;
        }
        $currentObject = $observer->getEvent()->getData("currentObject");
        if (isset($currentObject->stringData[self::INPUTDATA]) &&
            isset($currentObject->stringData[self::INPUTDATA]['priceLevel'])) {
            $priceLevelCode = $currentObject->stringData[self::INPUTDATA]['priceLevel'];
            $priceLevelData = $this->magentoPriceLevelFactory->create()
                            ->getCollection()->addFieldToFilter('pricelevel_code', $priceLevelCode)->getData();
            if (!empty($priceLevelData)) {
                $customer = $this->customerModel->create()->load($currentObject->customerId);
                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute('pricelevel', $priceLevelCode);
                $customer->updateData($customerData);
                $customer->save();
            } else {
                $this->logger->create()->createLog(
                    __METHOD__,
                    $priceLevelCode . ' Price level not exists in Magento',
                    'i95devApiException',
                    'critical'
                );
            }
        }
    }
}
