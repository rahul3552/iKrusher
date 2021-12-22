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
namespace Aheadworks\Ctq\Model\Quote\Status;

use Magento\Framework\DataObject;

/**
 * Class Restrictions
 * @package Aheadworks\Ctq\Model\Quote\Status
 */
class Restrictions extends DataObject implements RestrictionsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNextAvailableStatuses()
    {
        return $this->getData(self::NEXT_AVAILABLE_STATUSES);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerAvailableActions()
    {
        return $this->getData(self::SELLER_AVAILABLE_ACTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuyerAvailableActions()
    {
        return $this->getData(self::BUYER_AVAILABLE_ACTIONS);
    }
}
