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
namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\SellerPermissionManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class SellerPermissionService
 * @package Aheadworks\Ctq\Model\Service
 */
class SellerPermissionService implements SellerPermissionManagementInterface
{
    /**
     * @var RestrictionsPool
     */
    private $statusRestrictionsPool;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param RestrictionsPool $statusRestrictionsPool
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        RestrictionsPool $statusRestrictionsPool,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->statusRestrictionsPool = $statusRestrictionsPool;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function canBuyQuote($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $statusRestrictions = $this->statusRestrictionsPool->getRestrictions($quote->getStatus());

        return in_array(Status::ORDERED, $statusRestrictions->getNextAvailableStatuses());
    }
}
