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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Layout\Customer\Quote;

use Aheadworks\Ctq\Model\Layout\CrossMerger;
use Aheadworks\Ctq\Model\Layout\DefinitionFetcher;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class TotalsProcessor
 * @package Aheadworks\Ctq\Model\Layout\Customer\Quote
 */
class TotalsProcessor implements LayoutProcessorInterface
{
    /**
     * @var DefinitionFetcher
     */
    private $definitionFetcher;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CrossMerger
     */
    private $merger;

    /**
     * @param DefinitionFetcher $definitionFetcher
     * @param ArrayManager $arrayManager
     * @param CrossMerger $merger
     */
    public function __construct(
        DefinitionFetcher $definitionFetcher,
        ArrayManager $arrayManager,
        CrossMerger $merger
    ) {
        $this->definitionFetcher = $definitionFetcher;
        $this->arrayManager = $arrayManager;
        $this->merger = $merger;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $totalsPath = 'components/block-totals/children';

        $totalsLayout = $this->arrayManager->get($totalsPath, $jsLayout);
        if ($totalsLayout) {
            $totalsLayout = $this->addTotals($totalsLayout);
            $jsLayout = $this->arrayManager->set($totalsPath, $jsLayout, $totalsLayout);
        }
        return $jsLayout;
    }

    /**
     * Add totals definitions
     *
     * @param array $layout
     * @return array
     */
    private function addTotals(array $layout)
    {
        $path = '//referenceBlock[@name="checkout.cart.totals"]/arguments/argument[@name="jsLayout"]'
            . '/item[@name="components"]/item[@name="block-totals"]/item[@name="children"]';
        $layout = $this->merger->merge(
            $layout,
            $this->definitionFetcher->fetchArgs('checkout_cart_index', $path)
        );
        return $layout;
    }
}
