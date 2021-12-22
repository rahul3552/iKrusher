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
namespace Aheadworks\Ctq\Plugin\Model\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

/**
 * Class AbstractResetTotalPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Total
 */
class AbstractResetTotalPlugin implements ResetTotalInterface
{
    /**
     * @var bool
     */
    protected $discountUsed;

    /**
     * @var array
     */
    protected $items;

    /**
     * {@inheritdoc}
     */
    public function beforeCollect(
        AbstractTotal $subject,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $this->resetItems($quote, $shippingAssignment, $total);
        return [$quote, $shippingAssignment, $total];
    }

    /**
     * {@inheritdoc}
     */
    public function afterCollect(
        AbstractTotal $subject,
        AbstractTotal $result,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $this->restoreItems($quote, $shippingAssignment, $total);
        return $result;
    }

    /**
     * Reset shipping assignment items
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     */
    protected function resetItems(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($this->canBeApplied($quote)) {
            $this->items = $shippingAssignment->getItems();
            if ($shippingAssignment->getItems()) {
                $this->updateBeforeReset($quote, $shippingAssignment, $total);
                $shippingAssignment->setItems([]);
            }
        }
    }

    /**
     * Restore shipping assignment items
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     */
    protected function restoreItems(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($this->canBeApplied($quote) && $this->items) {
            $this->updateBeforeRestore($quote, $shippingAssignment, $total);
            $shippingAssignment->setItems($this->items);
        }
    }

    /**
     * Update before reset
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return bool
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        return true;
    }

    /**
     * Update before restore
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return bool
     */
    protected function updateBeforeRestore(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($this->discountUsed) {
            $quote->setAwCtqThrowException(true);
        }
        return true;
    }

    /**
     * Check if can be applied
     *
     * @param Quote $quote
     * @return bool
     */
    protected function canBeApplied($quote)
    {
        return $quote->getExtensionAttributes()
            && $quote->getExtensionAttributes()->getAwCtqQuote();
    }
}
