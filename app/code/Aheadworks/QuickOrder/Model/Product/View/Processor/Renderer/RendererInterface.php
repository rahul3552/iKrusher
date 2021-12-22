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

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface RendererInterface
 *
 * @package Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer
 */
interface RendererInterface
{
    /**
     * Render layout
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @param ProductInterface $product
     * @return $this
     */
    public function render($layout, $block, $product);
}
