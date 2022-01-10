<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Model\Config\Source\System;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Class Store
 * @package Mageplaza\ProductAttachments\Model\Config\Source\System
 */
class Store extends SystemStore implements ArrayInterface
{
    /**
     * @var SystemStore
     */
    protected $_systemStore;

    /**
     * Store constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param SystemStore $systemStore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SystemStore $systemStore
    ) {
        $this->_systemStore = $systemStore;

        parent::__construct($storeManager);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getStoreValuesForForm(false, true);
    }
}
