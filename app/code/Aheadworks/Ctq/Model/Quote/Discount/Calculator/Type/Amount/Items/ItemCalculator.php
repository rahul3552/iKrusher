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
namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\Amount\Items;

use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount\Items\AbstractItemCalculator;

/**
 * Class ItemCalculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\Amount\Items
 */
class ItemCalculator extends AbstractItemCalculator
{
    /**
     * Calculate available amount
     *
     * @param float $amount
     * @return $this
     */
    protected function calculateAvailableAmount($amount)
    {
        $baseAvailableAmount = min($this->metadata->getBaseItemsTotal(), $amount);

        $this->metadata
            ->setBaseAvailableAmount($baseAvailableAmount)
            ->setAvailableAmount($this->priceCurrency->convertAndRound($baseAvailableAmount));

        return $this;
    }
}
