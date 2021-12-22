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
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;
use Magento\Catalog\Model\Product\Configuration\Item\Option;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Downloadable\Helper\Catalog\Product\Configuration;

/**
 * Class DownloadableProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class DownloadableProvider extends DefaultProvider
{
    /**
     * @var Configuration
     */
    private $productConfig;

    /**
     * @param StockRegistryInterface $stockRegistry
     * @param IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
     * @param Configuration $productConfig
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty,
        Configuration $productConfig
    ) {
        parent::__construct($stockRegistry, $isProductSalableForRequestedQty);
        $this->productConfig = $productConfig;
    }

    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        $option = [];

        /** @var Option $option */
        $linkIds = $this->product->getCustomOption('downloadable_link_ids');
        if ($linkIds) {
            $itemLinks = [];
            $productLinks = $this->product->getTypeInstance()->getLinks($this->product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($productLinks[$linkId])) {
                    $itemLinks[] = $productLinks[$linkId];
                }
            }

            $option[] = $this->prepareOption($itemLinks);
        }

        return $option;
    }

    /**
     * Prepare option
     *
     * @param array $itemLinks
     * @return array
     */
    private function prepareOption($itemLinks)
    {
        $value = [];
        foreach ($itemLinks as $link) {
            $value[] = $link->getTitle();
        }

        return [
            'label' => $this->productConfig->getLinksTitle($this->product),
            'value' => implode(', ', $value)
        ];
    }
}
