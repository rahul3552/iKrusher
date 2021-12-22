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
namespace Aheadworks\Ctq\Model\Metadata\Negotiation;

use Magento\Framework\DataObject;

/**
 * Class Discount
 *
 * @package Aheadworks\Ctq\Model\Metadata\Negotiation
 */
class Discount extends DataObject implements NegotiatedDiscountInterface
{
    /**
     * @inheritdoc
     */
    public function getDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountType($discountType)
    {
        return $this->setData(self::DISCOUNT_TYPE, $discountType);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountValue()
    {
        return $this->getData(self::DISCOUNT_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountValue($discountValue)
    {
        return $this->setData(self::DISCOUNT_VALUE, $discountValue);
    }
}
