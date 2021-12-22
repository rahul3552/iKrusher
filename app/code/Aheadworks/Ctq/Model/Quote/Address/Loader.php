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
namespace Aheadworks\Ctq\Model\Quote\Address;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory as AddressCollectionFactory;

/**
 * Class Loader
 *
 * @package Aheadworks\Ctq\Model\Quote\Address
 */
class Loader
{
    /**
     * @var AddressCollectionFactory
     */
    private $addressCollectionFactory;

    /**
     * @param AddressCollectionFactory $addressCollectionFactory
     */
    public function __construct(
        AddressCollectionFactory $addressCollectionFactory
    ) {
        $this->addressCollectionFactory = $addressCollectionFactory;
    }

    /**
     * Load quote shipping address
     *
     * @param Quote $quote
     * @param string $addressType
     * @return AddressInterface
     */
    public function loadByType($quote, $addressType)
    {
        $addresses = $this->addressCollectionFactory->create()->setQuoteFilter($quote->getId());
        $shippingAddress = $quote->getShippingAddress();
        foreach ($addresses as $address) {
            if ($address->getAddressType() == $addressType && !$address->isDeleted()) {
                $shippingAddress = $address;
            }
        }

        return $shippingAddress;
    }
}
