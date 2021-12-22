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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Shipping;

use Magento\Sales\Block\Adminhtml\Order\Create\Form\Address as AddressForm;
use Magento\Sales\ViewModel\Customer\AddressFormatter;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Framework\Phrase;

/**
 * Class Address
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Shipping
 */
class Address extends AddressForm
{
    /**
     * Return header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Shipping Address');
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->setJsVariablePrefix('shippingAddress');
        parent::_prepareForm();

        $formValues = $this->getFormValues();
        if (!isset($formValues['country_id'])) {
            $this->_form->getElement('country_id')->setValue(null);
        }

        $this->_form->addFieldNameSuffix('shipping[shipping_address]');
        $this->_form->setHtmlNamePrefix('shipping[shipping_address]');
        $this->_form->setHtmlIdPrefix('quote-shipping_address_');

        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->getAddress()->getData();
    }

    /**
     * Return customer address id
     *
     * @return int|bool
     */
    public function getAddressId()
    {
        return $this->getAddress()->getCustomerAddressId();
    }

    /**
     * Return address object
     *
     * @return QuoteAddress
     */
    public function getAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Return is address disabled flag
     *
     * Return true is the quote is virtual
     *
     * @return bool
     */
    public function getIsDisabled()
    {
        return $this->getQuote()->isVirtual();
    }

    /**
     * Is need to display vat validation button
     *
     * @return bool
     */
    public function getDisplayVatValidationButton()
    {
        return false;
    }

    /**
     * Get list of addresses
     *
     * @return array
     */
    public function getAddressList()
    {
        $addressCollection = $this->getData('customerAddressCollection');

        $addressArray = [];
        if ($this->getCustomerId()) {
            $addressArray = $addressCollection->setCustomerFilter([$this->getCustomerId()])->toArray();
        }

        return $addressArray;
    }

    /**
     * Get list of addresses in JSON format
     *
     * @return string
     */
    public function getAddressesListAsJson()
    {
        $customerAddressFormatter = $this->getData('customerAddressFormatter');
        return $customerAddressFormatter->getAddressesJson($this->getAddressList());
    }

    /**
     * Format address data as string
     *
     * @param array $address
     * @return string
     */
    public function formatAddressAsString($address)
    {
        /** @var AddressFormatter $customerAddressFormatter */
        $customerAddressFormatter = $this->getData('customerAddressFormatter');
        return $customerAddressFormatter->getAddressAsString($address);
    }
}
