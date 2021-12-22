<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\Edit\Tab\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Block for displaying target information in customer view page
 * @api
 */
class CustomerInfo extends \Magento\Backend\Block\Template
{

    /**
     * Customer Repository
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    public $customerRepository;

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
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $baseHelper;

    public $_template = 'I95DevConnect_MessageQueue::customer/tab/view/custom_info.phtml';

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customerFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseHelper
     * @param \I95DevConnect\MessageQueue\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \I95DevConnect\MessageQueue\Helper\Data $baseHelper,
        \I95DevConnect\MessageQueue\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->baseHelper = $baseHelper;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve customer id
     * @return \Magento\Customer\Model\Customer
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
        return $this->baseHelper->getCustomAttribute(
            $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        );
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
        $customerId = (isset($customerCollection['target_customer_id']) ?
                $customerCollection['target_customer_id'] : '');
        $origin = (isset($customerCollection['origin']) ?
                $customerCollection['origin'] : '');
        if ($customerId == "" && $origin === null) {
            return false;
        }
        return true;
    }
}
