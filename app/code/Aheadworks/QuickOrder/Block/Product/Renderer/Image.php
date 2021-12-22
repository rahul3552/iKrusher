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
namespace Aheadworks\QuickOrder\Block\Product\Renderer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Block\Product\ImageBuilder;

/**
 * Class Image
 *
 * @package Aheadworks\QuickOrder\Block\Product\Renderer
 */
class Image extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_QuickOrder::product/renderer/image.phtml';

    /**
     * @var ImageBuilder
     */
    private $productImageBuilder;

    /**
     * @param Context $context
     * @param ImageBuilder $productImageBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        ImageBuilder $productImageBuilder,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->productImageBuilder = $productImageBuilder;
    }

    /**
     * Get product image
     *
     * @return string
     */
    public function getProductImage()
    {
        if ($product = $this->getProduct()) {
            return $this->productImageBuilder->setProduct($product)
                ->setImageId('category_page_grid')
                ->create()
                ->toHtml();
        }

        return '';
    }
}
