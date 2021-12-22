<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_TierPrice
 */

namespace I95DevConnect\TierPrice\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Product Data Provider class used for tier price
 */
class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider
{
    const APM = 'advanced_pricing_modal';
    const ARGUMENTS = 'arguments';
    const CONFIG = "config";
    const CHILDREN = "children";
    const PRICE = 'price';
    const FORMELEMENT = 'formElement';
    const COMPONENTTYPE = 'componentType';
    const PRICE_QTY = 'price_qty';
    const CUST_GROUP = 'cust_group';
    const VALUE_TYPE = 'value_type';
    const SELECT = 'select';
    const FIELD = 'field';
    const ACTIONDELETE = 'actionDelete';
    const WEBSITE_ID = "website_id";
    const DISABLED = "disabled";

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * ProductDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $pool);
    }

    /**
     * To get Meta Data
     * @return int
     * @author i95Dev Team
     * @updatedBy Subhan. Added componentType and formElement to fields
     */
    public function getMeta()
    {
        if ($this->dataHelper->isEnabled()) {
            $meta = parent::getMeta();

            if (isset($meta[self::APM]) && isset($meta[self::APM]
                    [self::CHILDREN]['advanced-pricing'][self::CHILDREN]
                    ['tier_price'][self::ARGUMENTS]['data'][self::CONFIG])) {

                $metaUpdated = [];

                $metaUpdated[self::ARGUMENTS]['data'][self::CONFIG][self::COMPONENTTYPE] = 'dynamicRows';
                $metaUpdated[self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;

                $metaSecondLevel[self::CHILDREN][self::WEBSITE_ID]
                [self::ARGUMENTS]['data'][self::CONFIG][self::FORMELEMENT] = self::SELECT;
                $metaSecondLevel[self::CHILDREN][self::WEBSITE_ID]
                [self::ARGUMENTS]['data'][self::CONFIG][self::COMPONENTTYPE] = self::FIELD;
                $metaSecondLevel[self::CHILDREN][self::WEBSITE_ID]
                [self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;

                $metaSecondLevel[self::CHILDREN][self::CUST_GROUP]
                [self::ARGUMENTS]['data'][self::CONFIG][self::FORMELEMENT] = self::SELECT;
                $metaSecondLevel[self::CHILDREN][self::CUST_GROUP]
                [self::ARGUMENTS]['data'][self::CONFIG][self::COMPONENTTYPE] = self::FIELD;
                $metaSecondLevel[self::CHILDREN][self::CUST_GROUP]
                [self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;

                $metaSecondLevel[self::CHILDREN][self::PRICE_QTY]
                [self::ARGUMENTS]['data'][self::CONFIG][self::FORMELEMENT] = 'input';
                $metaSecondLevel[self::CHILDREN][self::PRICE_QTY]
                [self::ARGUMENTS]['data'][self::CONFIG][self::COMPONENTTYPE] = self::FIELD;
                $metaSecondLevel[self::CHILDREN][self::PRICE_QTY]
                [self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;

                $metaSecondLevel[self::CHILDREN][self::VALUE_TYPE]
                [self::ARGUMENTS]['data'][self::CONFIG][self::FORMELEMENT] = self::SELECT;
                $metaSecondLevel[self::CHILDREN][self::VALUE_TYPE]
                [self::ARGUMENTS]['data'][self::CONFIG][self::COMPONENTTYPE] = self::FIELD;
                $metaSecondLevel[self::CHILDREN][self::VALUE_TYPE]
                [self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;

                $metaSecondLevel[self::CHILDREN][self::PRICE][self::ARGUMENTS]
                ['data'][self::CONFIG][self::FORMELEMENT] = 'input';
                $metaSecondLevel[self::CHILDREN][self::PRICE][self::ARGUMENTS]
                ['data'][self::CONFIG][self::COMPONENTTYPE] = self::FIELD;
                $metaSecondLevel[self::CHILDREN][self::PRICE][self::ARGUMENTS]
                ['data'][self::CONFIG][self::DISABLED] = 1;

                $metaSecondLevel[self::CHILDREN][self::ACTIONDELETE][self::ARGUMENTS]['data']
                [self::CONFIG][self::COMPONENTTYPE] = self::ACTIONDELETE;
                $metaSecondLevel[self::CHILDREN][self::ACTIONDELETE]
                [self::ARGUMENTS]['data'][self::CONFIG][self::DISABLED] = 1;

                $metaSecondLevel[self::ARGUMENTS]['data'][self::CONFIG][self::COMPONENTTYPE] = 'container';
                $metaUpdated[self::CHILDREN]['record'] = $metaSecondLevel;

                $meta[self::APM][self::CHILDREN]['advanced-pricing'][self::CHILDREN]['tier_price'] =
                    $metaUpdated;

            }
            return $meta;
        } else {
            return parent::getMeta();
        }
    }
}
