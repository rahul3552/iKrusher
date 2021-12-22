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

use Magento\Framework\Exception\LocalizedException;

/**
 * Class BundleProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class BundleProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return isset($orderOptions['bundle_options']) ? array_values($orderOptions['bundle_options']) : [];
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function getQtySalableMessage($requestedQty)
    {
        $message = '';
        foreach ($this->subProducts as $product) {
            $qty = $product->getCartQty() * $requestedQty;
            $resultMessage = $this->getSalableResultMessageForSku($product->getSku(), $qty);
            if ($resultMessage) {
                $message = $resultMessage;
                break;
            }
        }

        return $message;
    }
}
