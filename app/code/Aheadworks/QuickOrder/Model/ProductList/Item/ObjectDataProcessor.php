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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\ProductList\Item;

use Aheadworks\QuickOrder\Model\ProductList\Item as ItemModel;
use Aheadworks\QuickOrder\Model\Product\Option\Serializer as OptionSerializer;

/**
 * Class ObjectDataProcessor
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item
 */
class ObjectDataProcessor
{
    /**
     * @var OptionSerializer
     */
    private $optionSerializer;

    /**
     * @param OptionSerializer $optionSerializer
     */
    public function __construct(
        OptionSerializer $optionSerializer
    ) {
        $this->optionSerializer = $optionSerializer;
    }

    /**
     * Prepare entity data before save
     *
     * @param ItemModel $item
     * @return ItemModel
     */
    public function prepareDataBeforeSave($item)
    {
        $item->setProductOption($this->optionSerializer->serializeToString($item->getProductOption()));
        return $item;
    }

    /**
     * Prepare entity data after load
     *
     * @param ItemModel $item
     * @return ItemModel
     */
    public function prepareDataAfterLoad($item)
    {
        $item->setProductOption($this->optionSerializer->unserializeToObject($item->getProductOption()));
        return $item;
    }
}
