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
namespace Aheadworks\Ctq\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class DataProvider
 * @package Aheadworks\Ctq\Model\Order
 */
class DataProvider
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Retrieve order
     *
     * @param int $orderId
     * @return OrderInterface|null
     */
    public function getOrder($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (LocalizedException $e) {
            $order = null;
        }

        return $order;
    }

    /**
     * Retrieve order increment id
     *
     * @param int $orderId
     * @return string|null
     */
    public function getOrderIncrementId($orderId)
    {
        $order = $this->getOrder($orderId);

        return $order ? $order->getIncrementId() : '';
    }
}
