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
namespace Aheadworks\QuickOrder\Model\Product\Option\Bundle;

use Magento\Framework\DataObject;
use Magento\Bundle\Api\Data\BundleOptionInterface;
use Magento\Bundle\Model\ProductOptionProcessor;
use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\QuickOrder\Model\Product\Option\Bundle
 */
class Processor extends ProductOptionProcessor
{
    /**
     * @inheritdoc
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();

        $bundleOptions = $this->getBundleOptions($productOption);
        if (!empty($bundleOptions) && is_array($bundleOptions)) {
            $requestData = [];
            foreach ($bundleOptions as $option) {
                /** @var BundleOptionInterface $option */
                foreach ($option->getOptionSelections() as $selection) {
                    $requestData['bundle_option'][$option->getOptionId()] = $selection;
                    $requestData['bundle_option_qty'][$option->getOptionId()] = $option->getOptionQty();
                }
            }
            $request->addData($requestData);
        }

        return $request;
    }
}
