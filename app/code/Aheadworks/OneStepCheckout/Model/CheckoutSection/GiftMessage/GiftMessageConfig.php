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
namespace Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage;

use Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class GiftMessageConfig
 * @package Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage
 */
class GiftMessageConfig extends AbstractSimpleObject implements GiftMessageConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->_get(self::IS_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->setData(self::IS_ENABLED, $isEnabled);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }
}
