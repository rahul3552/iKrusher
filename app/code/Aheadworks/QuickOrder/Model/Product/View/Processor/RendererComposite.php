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
namespace Aheadworks\QuickOrder\Model\Product\View\Processor;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;
use Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer\RendererInterface;

/**
 * Class RendererComposite
 *
 * @package Aheadworks\QuickOrder\Model\Product\View\Processor
 */
class RendererComposite
{
    /**
     * Base block
     */
    const BASE_BLOCK = 'product.info.options.wrapper';
    const BASE_BLOCK_TEMPLATE = 'Aheadworks_QuickOrder::product/popup.phtml';

    /**
     * @var RendererInterface[]
     */
    private $rendererList = [];

    /**
     * @param array $rendererList
     */
    public function __construct(
        $rendererList = []
    ) {
        $this->rendererList = $rendererList;
    }

    /**
     * Render layout
     *
     * @param LayoutInterface $layout
     * @return string
     */
    public function render($layout)
    {
        $result = '';
        $block = $layout->getBlock(self::BASE_BLOCK);
        if ($block instanceof Template) {
            /** @var Template $block */
            $block->setTemplate(self::BASE_BLOCK_TEMPLATE);
            foreach ($this->rendererList as $renderer) {
                $renderer->render($layout, $block, $block->getProduct());
            }
            $result = $block->toHtml();
        }

        return $result;
    }
}
