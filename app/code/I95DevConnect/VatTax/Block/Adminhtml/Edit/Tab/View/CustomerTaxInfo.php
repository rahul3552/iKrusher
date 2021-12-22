<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml\Edit\Tab\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\CustomerFactory;

/**
 * Block for displaying tax business group information of customer
 * @api
 */
class CustomerTaxInfo extends \I95DevConnect\MessageQueue\Block\Adminhtml\Edit\Tab\View\CustomerInfo
{

    /**
     * Customer
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
    public $taxHelper;
    public $customerFactory;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $baseHelper;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Config
     */
    public $configHelper;

    public $_template = 'I95DevConnect_VatTax::customer/tab/view/tax_info.phtml';

    /**
     * CustomerTaxInfo constructor.
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customerFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseHelper
     * @param \I95DevConnect\VatTax\Helper\Data $taxHelper
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
        \I95DevConnect\VatTax\Helper\Data $taxHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerRepository;
        $this->baseHelper = $baseHelper;
        $this->taxHelper = $taxHelper;
        $this->configHelper = $configHelper;
        parent::__construct(
            $context,
            $registry,
            $customerRepository,
            $customerFactory,
            $baseHelper,
            $configHelper,
            $data
        );
    }

    /**
     * get customer price level
     * @return string
     */
    public function getCustomerTax()
    {
        $businessTax = '';
        if ($this->taxHelper->isVatTaxEnabled()) {
            $businessTax = $this->getCustomer()->getTaxBusPostingGroup();
        }
        return $businessTax;
    }

    /**
     * Check is extension enabled
     * @return mixed
     */
    public function isEnable()
    {
        return $this->taxHelper->isVatTaxEnabled();
    }
}
