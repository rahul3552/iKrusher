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
namespace Aheadworks\CreditLimit\Model\Customer\Backend;

use Magento\User\Model\ResourceModel\User\Collection;
use Magento\User\Model\ResourceModel\User\CollectionFactory;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Ui\Component\Listing\Customer\Bookmark\OutstandingBalance;
use Magento\User\Model\User as AdminUser;

/**
 * Class BookmarkInstaller
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Backend
 */
class BookmarkInstaller
{
    /**
     * @var CollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var OutstandingBalance
     */
    private $outstandingBalanceBookmark;

    /**
     * @var BookmarkRepositoryInterface
     */
    private $bookmarkRepository;

    /**
     * @param CollectionFactory $userCollectionFactory
     * @param OutstandingBalance $outstandingBalanceBookmark
     * @param BookmarkRepositoryInterface $bookmarkRepository
     */
    public function __construct(
        CollectionFactory $userCollectionFactory,
        OutstandingBalance $outstandingBalanceBookmark,
        BookmarkRepositoryInterface $bookmarkRepository
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->outstandingBalanceBookmark = $outstandingBalanceBookmark;
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * Create necessary bookmarks
     *
     * @throws LocalizedException
     */
    public function install()
    {
        /** @var Collection $userCollection */
        $userCollection = $this->userCollectionFactory->create();
        $adminUsers = $userCollection->getItems();

        /** @var AdminUser $adminUser */
        foreach ($adminUsers as $adminUser) {
            $bookmark = $this->outstandingBalanceBookmark->create($adminUser);
            $this->bookmarkRepository->save($bookmark);
        }
    }
}
