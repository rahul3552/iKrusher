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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Sales;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class OrderFinder
 *
 * @package Aheadworks\CreditLimit\Model\Sales
 */
class OrderFinder
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Find order by its increment ID
     *
     * @param int $orderIncrementId
     * @return OrderInterface
     */
    public function findOrderByIncrementId($orderIncrementId)
    {
        $this->searchCriteriaBuilder->addFilter(
            OrderInterface::INCREMENT_ID,
            $orderIncrementId
        );
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result = $this->orderRepository->getList($searchCriteria);
        $items = $result->getItems();

        return reset($items);
    }
}
