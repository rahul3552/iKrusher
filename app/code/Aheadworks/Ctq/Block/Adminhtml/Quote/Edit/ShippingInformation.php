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
namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Phrase;

/**
 * Class ShippingInformation
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class ShippingInformation extends AbstractEdit
{
    /**
     * @var array
     */
    private $requiredAddressFields = [
        'firstname',
        'lastname',
        'street',
        'city',
        'country_id'
    ];

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_shipping_information');
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Shipping Information');
    }

    /**
     * Check if address is specified by customer
     *
     * @return bool
     */
    public function isShippingInfoSpecified()
    {
        $address = $this->getQuote()->getShippingAddress();
        foreach ($this->requiredAddressFields as $requiredField) {
            if (!$address->getData($requiredField)) {
                return false;
            }
        }

        return true;
    }
}
