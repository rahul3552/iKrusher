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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Cart;

use Aheadworks\OneStepCheckout\Model\Product\ConfigurationPool;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Class OptionsProvider
 * @package Aheadworks\OneStepCheckout\Model\Cart
 */
class OptionsProvider
{
    /**
     * @var CartItemRepositoryInterface
     */
    private $quoteItemRepository;

    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @param CartItemRepositoryInterface $quoteItemRepository
     * @param ConfigurationPool $configurationPool
     */
    public function __construct(
        CartItemRepositoryInterface $quoteItemRepository,
        ConfigurationPool $configurationPool
    ) {
        $this->quoteItemRepository = $quoteItemRepository;
        $this->configurationPool = $configurationPool;
    }

    /**
     * Get editable cart item options
     *
     * @param int $cartId
     * @return array
     */
    public function getOptionsData($cartId)
    {
        $optionsData = [];
        /** @var CartItemInterface|Item $item */
        foreach ($this->quoteItemRepository->getList($cartId) as $item) {
            $productType = $item->getProductType();
            if ($this->configurationPool->hasConfiguration($productType)) {
                $configuration = $this->configurationPool->getConfiguration($productType);
                $optionsData[$item->getItemId()] = [
                    'product_type' => $productType,
                    'options' => $configuration->getOptions($item)
                ];
            }
        }
        return $optionsData;
    }
}
