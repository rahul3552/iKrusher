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
namespace Aheadworks\QuickOrder\Model\Toolbar\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\QuickOrder\Model\Toolbar\Layout\LayoutProcessorInterface;
use Aheadworks\QuickOrder\Model\Url;

/**
 * Class Config
 *
 * @package Aheadworks\QuickOrder\Model\Toolbar\Layout\Processor
 */
class Config implements LayoutProcessorInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     * @param Url $url
     */
    public function __construct(
        ArrayManager $arrayManager,
        Url $url
    ) {
        $this->arrayManager = $arrayManager;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $component = 'components/aw_quick_order_config';
        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'addToListUrl' => $this->url->getAddToListUrl(),
                'multipleAddToListUrl' => $this->url->getMultipleAddToListUrl(),
                'configureItemUrl' => $this->url->getConfigureItemUrl(),
                'updateItemOptionUrl' => $this->url->getUpdateItemOptionUrl(),
                'updateItemQtyUrl' => $this->url->getUpdateItemQtyUrl(),
                'removeItemUrl' => $this->url->getRemoveItemUrl(),
            ]
        );

        return $jsLayout;
    }
}
