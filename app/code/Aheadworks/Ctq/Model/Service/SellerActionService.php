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

use Aheadworks\Ctq\Api\SellerActionManagementInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Quote\Action\ActionManagement;

/**
 * Class SellerActionService
 * @package Aheadworks\Ctq\Model\Service
 */
class SellerActionService implements SellerActionManagementInterface
{
    /**
     * @var RestrictionsPool
     */
    private $statusRestrictionsPool;

    /**
     * @var ActionManagement
     */
    private $actionManagement;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param RestrictionsPool $statusRestrictionsPool
     * @param ActionManagement $actionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        RestrictionsPool $statusRestrictionsPool,
        ActionManagement $actionManagement,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->statusRestrictionsPool = $statusRestrictionsPool;
        $this->actionManagement = $actionManagement;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableQuoteActions($quote)
    {
        $quote = $quote instanceof QuoteInterface ? $quote : $this->quoteRepository->get($quote);
        $statusRestrictions = $this->statusRestrictionsPool->getRestrictions($quote->getStatus());

        return $this->actionManagement->getActionObjects($statusRestrictions->getSellerAvailableActions());
    }
}
