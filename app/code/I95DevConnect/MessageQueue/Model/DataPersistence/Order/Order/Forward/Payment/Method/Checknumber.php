<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Payment\Method;

/**
 * Class for Checknumber payment method data in order result to ERP
 */
class Checknumber extends \Magento\OfflinePayments\Model\Checkmo
{

    const PAYMENT_METHOD_CHECKMO_CODE = 'checkmo';

    /**
     * Payment method code
     *
     * @var string
     */
    public $code = self::PAYMENT_METHOD_CHECKMO_CODE;

    /**
     * @var string
     */
    public $formBlockType = Magento\OfflinePayments\Block\Form\Checkmo::class;

    /**
     * @var string
     */
    public $infoBlockType = Magento\OfflinePayments\Block\Info\Checkmo::class;

    /**
     * Availability option
     *
     * @var bool
     */
    public $isOffline = true;

    /**
     *
     * @var \Magento\Framework\DataObjectFactory
     */
    public $dataObjectFactory;

    /* Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    public $_eventManager;

    /* Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * Assign payment data to payment entity
     *
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getPayableTo()
    {
        return $this->getConfigData('payable_to');
    }

    /**
     * @return string
     */
    public function getMailingAddress()
    {
        return $this->getConfigData('mailing_address');
    }

    /**
     * Assign data to info model instance
     *
     * @param  \Magento\Framework\DataObject|mixed $data
     * @return $this
     * @throws \Exception
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        if (!$data instanceof \Magento\Framework\DataObject) {
            $data = $this->dataObjectFactory->create($data);
        }
        $datas = $data->getAdditionalData();

        $this->getInfoInstance()->setAdditionalInformation('Checknumber', $datas['check_number']);
        return $this;
    }
}
