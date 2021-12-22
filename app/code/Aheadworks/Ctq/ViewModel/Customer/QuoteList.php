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
namespace Aheadworks\Ctq\ViewModel\Customer;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\ResourceModel\Quote\Collection;
use Aheadworks\Ctq\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Customer\Model\Session;

/**
 * Class QuoteList
 * @package Aheadworks\Ctq\ViewModel\Customer
 */
class QuoteList implements ArgumentInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Collection|null
     */
    private $quoteList;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Session $customerSession
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Session $customerSession
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Retrieve quote list
     *
     * @return Collection|null
     */
    public function getQuoteList()
    {
        if (null === $this->quoteList) {
            $this->quoteList = $this->collectionFactory->create();
            $this->quoteList
                ->addFieldToFilter(
                    QuoteInterface::CUSTOMER_ID,
                    ['eq' => $this->customerSession->getCustomerId()]
                )
                ->addOrder(QuoteInterface::LAST_UPDATED_AT, Collection::SORT_ORDER_DESC);
        }

        return $this->quoteList;
    }
}
