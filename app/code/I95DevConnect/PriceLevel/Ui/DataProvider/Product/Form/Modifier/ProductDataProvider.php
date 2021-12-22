<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use I95DevConnect\PriceLevel\Helper\Data;

/**
 * Class for Product Advance Pricing
 */
class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider
{
    const CHILDREN = "children";
    const ADVANCED_PRICING_MODEL = 'advanced_pricing_modal';
    const ADVANCED_PRICING = 'advanced-pricing';
    const TIER_PRICE = 'tier_price';
    const RECORD = 'record';
    const ARGUMENTS = 'arguments';
    const CONFIG = "config";
    const DISABLED = "disabled";

    public $helper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param Data $helper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        Data $helper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $pool, $meta, $data);
        $this->helper = $helper;
    }
    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        if ($this->helper->isEnabled() && isset($meta[self::ADVANCED_PRICING_MODEL]) &&
                    isset($meta[self::ADVANCED_PRICING_MODEL][self::CHILDREN][self::ADVANCED_PRICING]
                            [self::CHILDREN][self::TIER_PRICE][self::ARGUMENTS]['data'][self::CONFIG])) {

            $metaTierPrice = $meta[self::ADVANCED_PRICING_MODEL][self::CHILDREN]
            [self::ADVANCED_PRICING][self::CHILDREN][self::TIER_PRICE];
            $metaTierPrice[self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;
            $metaTierPriceChild = $metaTierPrice[self::CHILDREN][self::RECORD][self::CHILDREN];
            $metaTierPriceChild['website_id'][self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;
            $metaTierPriceChild['cust_group'][self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;
            $metaTierPriceChild['price_qty'][self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;
            $metaTierPriceChild['price'][self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;
            $metaTierPriceChild['actionDelete'][self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;

        }

        return $meta;
    }
}
