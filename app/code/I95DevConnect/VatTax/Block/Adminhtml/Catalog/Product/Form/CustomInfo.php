<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml\Catalog\Product\Form;

/**
 * Block for displaying tax posting group information in product
 */
class CustomInfo extends \Magento\Framework\View\Element\Template
{

    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'product/custom_info.phtml';

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
    public $taxHelper;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \I95DevConnect\VatTax\Helper\Data $taxHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product $productModel,
        \I95DevConnect\VatTax\Helper\Data $taxHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->productModel = $productModel;
        $this->taxHelper = $taxHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve product id
     *
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
    public function getCustomProductAttribute()
    {
        $id = $this->getProductId();
        $res = $this->productModel->load($id);
        return $res['tax_product_posting_group'];
    }

    /**
     * Check is extension enabled
     * @return mixed
     */
    public function isEnable()
    {
        return $this->taxHelper->isVatTaxEnabled();
    }
}
