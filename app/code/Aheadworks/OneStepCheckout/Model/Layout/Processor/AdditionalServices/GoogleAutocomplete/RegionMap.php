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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices\GoogleAutocomplete;

use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

/**
 * Class RegionMap
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices\GoogleAutocomplete
 */
class RegionMap
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get region map
     *
     * @return array
     */
    public function getMap()
    {
        $map = [];
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        foreach ($collection as $item) {
            $map[] = [
                'code' => $item->getCode(),
                'countryId' => $item->getCountryId(),
                'id' => $item->getRegionId()
            ];
        }
        return $map;
    }
}
