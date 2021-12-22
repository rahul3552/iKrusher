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
namespace Aheadworks\QuickOrder\Model\ResourceModel\ProductList;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList as ProductListResource;
use Aheadworks\QuickOrder\Model\ProductList as ProductListModel;

/**
 * Class Collection
 *
 * @package Aheadworks\QuickOrder\Model\ResourceModel\ProductList
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ProductListModel::class, ProductListResource::class);
    }
}
