<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\NetTerms\Block\Adminhtml\Order\Create;

use I95DevConnect\NetTerms\Model\NetTermsFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\Form;
use Magento\Sales\Model\AdminOrder\Create;

/**
 * Block class to get netterms in order creation
 */
class NetTerms extends Form
{

    public $customerFactory;

    public $netTermsFactory;

    public function __construct( // NOSONAR
        Context $context,
        Quote $sessionQuote,
        CustomerFactory $customerFactory,
        NetTermsFactory $netTermsFactory,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        EncoderInterface $jsonEncoder,
        FormFactory $customerFormFactory,
        CustomerRepositoryInterface $customerRepository,
        CurrencyInterface $localeCurrency,
        Mapper $addressMapper,
        array $data = [] // NOSONAR
    ) {
        $this->customerFactory = $customerFactory;
        $this->netTermsFactory = $netTermsFactory;

        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $jsonEncoder, $customerFormFactory, $customerRepository, $localeCurrency, $addressMapper);
    }

    /**
     * Return Header CSS Class
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-account';
    }

    /**
     * Return header text
     * @return string
     */
    public function getHeaderText()
    {
        return __('I95Dev Net Terms');
    }

    /**
     * Return customer id
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getQuote()->getCustomerId();
    }

    /**
     * Returns customer netterm id
     * @return string
     */
    public function getCustomerNetTermId()
    {
        $customerId = $this->getCustomerId();
        if (isset($customerId)) {
            $customer = $this->customerFactory->create()->load($customerId);
            return $customer['net_terms_id'];
        }
    }

    /**
     * Returns netterms collection
     * @return obj
     */
    public function getNetTermsData()
    {
        return $this->netTermsFactory->create()->getCollection();
    }
}
