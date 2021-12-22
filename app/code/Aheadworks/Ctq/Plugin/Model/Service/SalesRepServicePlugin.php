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
namespace Aheadworks\Ctq\Plugin\Model\Service;

use Aheadworks\Ctq\Model\QuoteRepository;
use Aheadworks\SalesRepresentative\Model\Service\SalesRepService;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class SalesRepServicePlugin
 * @package Aheadworks\Ctq\Plugin\Model\Service
 */
class SalesRepServicePlugin
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Change sales representative id if order was created by quote
     *
     * @param SalesRepService $service
     * @param int $salesRepId
     * @param int|OrderInterface $order
     * @param bool $needToNotify
     * @return array
     */
    public function beforeAssignSalesRepToOrder(
        SalesRepService $service,
        $salesRepId,
        $order,
        $needToNotify
    ) {
        if ($order instanceof OrderInterface) {
            try {
                $quote = $this->quoteRepository->getByOrderId($order->getEntityId());
            } catch (NoSuchEntityException $e) {
                $quote = null;
            }
            $salesRepId = $quote ? $quote->getSellerId() : $salesRepId;
        }

        return [$salesRepId, $order, $needToNotify];
    }
}
