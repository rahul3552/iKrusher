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
namespace Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer;

use Aheadworks\QuickOrder\Block\Product\Renderer\Image as ProductImage;

/**
 * Class Image
 *
 * @package Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer
 */
class Image implements RendererInterface
{
    /**
     * @inheritdoc
     */
    public function render($layout, $block, $product)
    {
        $imageBlock = $layout->createBlock(
            ProductImage::class,
            'aw_qo.popup.product-image',
            ['data' => ['product' => $product]]
        );
        $block->append($imageBlock, 'product_image');

        return $this;
    }
}
