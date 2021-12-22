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
namespace Aheadworks\Ctq\ViewModel\History;

use Aheadworks\Ctq\Api\Data\HistorySearchResultsInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\HistoryRepositoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class HistoryList
 * @package Aheadworks\Ctq\ViewModel\History
 */
class HistoryList implements ArgumentInterface
{
    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var HistorySearchResultsInterface|null
     */
    private $historySearchResults;

    /**
     * @param HistoryRepositoryInterface $historyRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->historyRepository = $historyRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Retrieve history search results
     *
     * @param int $quoteId
     * @return HistorySearchResultsInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHistorySearchResults($quoteId)
    {
        if (null === $this->historySearchResults) {
            $sortOrder = $this->sortOrderBuilder
                ->setField(HistoryInterface::CREATED_AT)
                ->setDirection(SortOrder::SORT_DESC)
                ->create();

            $this->searchCriteriaBuilder
                ->addFilter(HistoryInterface::QUOTE_ID, ['eq' => $quoteId])
                ->addSortOrder($sortOrder);

            $this->historySearchResults = $this->historyRepository->getList($this->searchCriteriaBuilder->create());
        }

        return $this->historySearchResults;
    }
}
