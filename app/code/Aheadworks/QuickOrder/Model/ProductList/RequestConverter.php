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
namespace Aheadworks\QuickOrder\Model\ProductList;

use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;

/**
 * Class RequestConverter
 *
 * @package Aheadworks\QuickOrder\Model\ProductList
 */
class RequestConverter
{
    /**
     * Convert sku list to request items
     *
     * @param string $skuList
     * @return array
     */
    public function convertSkuListToRequestItems($skuList)
    {
        $requestItems = array_map(
            function ($item) {
                return [
                    ItemDataInterface::PRODUCT_SKU => trim($item)
                ];
            },
            explode("\n", $skuList)
        );

        return $requestItems;
    }

    /**
     * Convert csv lines to request items
     *
     * @param array $csvLines
     * @return array
     */
    public function convertCsvLinesToRequestItems($csvLines)
    {
        $requestItems = array_map(
            function ($item) {
                return [
                    ItemDataInterface::PRODUCT_SKU => trim($item[0]),
                    ItemDataInterface::PRODUCT_QTY => trim($item[1])
                ];
            },
            $this->filterCsvLines($csvLines)
        );

        return $requestItems;
    }

    /**
     * Filter csv lines
     *
     * @param array $csvLines
     * @return array
     */
    private function filterCsvLines($csvLines)
    {
        $result = [];
        foreach ($csvLines as $line) {
            if (isset($line[0])) {
                if (!isset($line[1])) {
                    $line[1] = 1;
                }
                $result[] = $line;
            }
        }

        return $result;
    }
}
