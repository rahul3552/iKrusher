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

use Magento\Framework\Escaper;
use Magento\Catalog\Model\Product\Configuration\Item\Option;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class EnterpriseGiftCardProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class EnterpriseGiftCardProvider extends DefaultProvider
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param StockRegistryInterface $stockRegistry
     * @param IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
     * @param Escaper $escaper
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty,
        Escaper $escaper
    ) {
        parent::__construct($stockRegistry, $isProductSalableForRequestedQty);
        $this->escaper = $escaper;
    }

    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return $this->getGiftcardOptions();
    }

    /**
     * Prepare custom option to display, returns false if there's no value
     *
     * @param string $code
     * @return string|false
     */
    private function prepareCustomOption($code)
    {
         /** @var Option $option */
        $option = $this->product->getCustomOption($code);
        if ($option) {
            $value = $option->getValue();
            if ($value) {
                return $this->escaper->escapeHtml($value);
            }
        }

        return false;
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    public function getGiftcardOptions()
    {
        $result = [];
        $value = $this->prepareCustomOption('giftcard_sender_name');
        if ($value) {
            $email = $this->prepareCustomOption('giftcard_sender_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = ['label' => __('Gift Card Sender'), 'value' => $value];
        }

        $value = $this->prepareCustomOption('giftcard_recipient_name');
        if ($value) {
            $email = $this->prepareCustomOption('giftcard_recipient_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = ['label' => __('Gift Card Recipient'), 'value' => $value];
        }

        $value = $this->prepareCustomOption('giftcard_message');
        if ($value) {
            $result[] = ['label' => __('Gift Card Message'), 'value' => $value];
        }

        return $result;
    }
}
