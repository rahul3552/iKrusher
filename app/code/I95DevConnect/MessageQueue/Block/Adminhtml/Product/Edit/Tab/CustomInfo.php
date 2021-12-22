<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;

/**
 * Block for displaying target information in product
 * @api
 */
class CustomInfo extends \Magento\Backend\Block\Template
{

    public $_template = 'I95DevConnect_MessageQueue::product/tab/edit/custom_info.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Product Model
     *
     * @var \Magento\Catalog\Model\Product
     */
    public $productModel;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $helperData;

    /**
     *
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \I95DevConnect\MessageQueue\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product $productModel,
        \I95DevConnect\MessageQueue\Helper\Data $helperData,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->productModel = $productModel;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve product id
     * @return string|null
     */
    public function getProductId()
    {
        $product = $this->coreRegistry->registry('current_product');
        return $product->getId();
    }

    /**
     * get product custom attribute
     * @return string
     */
    public function getCustomAttribute()
    {
        $targetProductStatus = null;
        if ($this->helperData->isEnabled()) {
            $id = $this->getProductId();
            $res = $this->productModel->load($id);
            $targetProductStatus = $res['targetproductstatus'];
        }
        return $targetProductStatus;
    }
}
