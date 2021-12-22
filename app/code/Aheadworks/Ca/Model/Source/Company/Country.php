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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Source\Company;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Country
 * @package Aheadworks\Ca\Model\Source\Company
 */
class Country implements OptionSourceInterface
{
    /**
     * @var CountryCollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @inheritDoc
     */
    public function __construct(
        CountryCollectionFactory $countryCollection,
        StoreManagerInterface $storeManager
    ) {
        $this->countryCollectionFactory = $countryCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function toOptionArray()
    {
        return $this->countryCollectionFactory->create()
            ->loadByStore($this->storeManager->getStore()->getId())
            ->toOptionArray();
    }
}
