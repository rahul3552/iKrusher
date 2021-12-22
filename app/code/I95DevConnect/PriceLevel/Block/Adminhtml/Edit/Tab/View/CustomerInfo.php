<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Block\Adminhtml\Edit\Tab\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\CustomerFactory;

/**
 * Block for displaying target information in customer view page
 * @api
 */
class CustomerInfo extends \Magento\Backend\Block\Template
{

    const TARGET_CUSTOMER_ID = 'target_customer_id';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $messageQueueHelper;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Config
     */
    public $configHelper;

    /**
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $baseHelper;

    public $_template = 'I95DevConnect_PriceLevel::customer/tab/view/custom_info.phtml';

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param CustomerFactory $customerFactory
     * @param \I95DevConnect\PriceLevel\Helper\Data $baseHelper
     * @param \I95DevConnect\MessageQueue\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \I95DevConnect\PriceLevel\Helper\Data $baseHelper,
        \I95DevConnect\MessageQueue\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerFactory = $customerFactory;
        $this->baseHelper = $baseHelper;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve customer id
     * @return \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function getCustomer()
    {
        $customerId = $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $this->customerFactory->create()->load($customerId);
    }

    /**
     * get customer attribute
     * @return string
     */
    public function getCustomAttribute()
    {
        $customerCollection = $this->customerFactory->create()
                ->load($this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID))->getData();
        return isset($customerCollection[self::TARGET_CUSTOMER_ID]) ?
                $customerCollection[self::TARGET_CUSTOMER_ID] : "Customer Sync In Process";
    }

    /**
     * get customer price level
     * @return string
     */
    public function getCustomerPricelevel()
    {
        $priceLevel = '';
        if ($this->baseHelper->isEnabled()) {
            $priceLevel = $this->getCustomer()->getPricelevel();
        }
        return $priceLevel;
    }

    /**
     * get component data
     * @return string
     */
    public function getComponent()
    {
        $configurationValues = $this->configHelper->getConfigValues()->getData();
        return $configurationValues['component'];
    }

    /**
     * check custom attribute of customers
     * @return boolean
     */
    public function checkCustomAttribute()
    {
        $customerCollection = $this->customerFactory->create()
                ->load($this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID))->getData();
        $customerId = (isset($customerCollection[self::TARGET_CUSTOMER_ID]) ?
                $customerCollection[self::TARGET_CUSTOMER_ID] : '');
        $origin = (isset($customerCollection['origin']) ?
                $customerCollection['origin'] : '');
        if (empty($customerId) && empty($origin)) {
            return false;
        }
        return true;
    }
}
