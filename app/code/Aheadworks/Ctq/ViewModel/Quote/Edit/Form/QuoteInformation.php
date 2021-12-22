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
namespace Aheadworks\Ctq\ViewModel\Quote\Edit\Form;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\ThirdPartyModule\Aheadworks\Ca\CompanyManagement;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Aheadworks\Ctq\Model\Source\Quote\Status as StatusSource;
use Aheadworks\Ctq\Model\Quote\Expiration\Calculator as ExpirationCalculator;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Model\Source\Admin\User as AdminUserSource;
use Aheadworks\Ctq\Model\Config;
use Magento\Customer\Api\GroupRepositoryInterface;
use Aheadworks\Ctq\Model\Order\DataProvider as OrderDataProvider;

/**
 * Class QuoteInformation
 *
 * @package Aheadworks\Ctq\ViewModel\Quote\Edit\Form
 */
class QuoteInformation implements ArgumentInterface
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * @var ExpirationCalculator
     */
    private $expirationCalculator;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var AdminUserSource
     */
    private $adminUserSource;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var OrderDataProvider
     */
    private $orderDataProvider;

    /**
     * @var CompanyManagement
     */
    private $companyManagement;

    /**
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param UrlInterface $urlBuilder
     * @param TimezoneInterface $localeDate
     * @param QuoteSession $quoteSession
     * @param StatusSource $statusSource
     * @param ExpirationCalculator $expirationCalculator
     * @param QuoteInterfaceFactory $quoteFactory
     * @param AdminUserSource $adminUserSource
     * @param Config $config
     * @param GroupRepositoryInterface $groupRepository
     * @param OrderDataProvider $orderDataProvider
     * @param CompanyManagement $companyManagement
     */
    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        CustomerRepositoryInterface $customerRepository,
        UrlInterface $urlBuilder,
        TimezoneInterface $localeDate,
        QuoteSession $quoteSession,
        StatusSource $statusSource,
        ExpirationCalculator $expirationCalculator,
        QuoteInterfaceFactory $quoteFactory,
        AdminUserSource $adminUserSource,
        Config $config,
        GroupRepositoryInterface $groupRepository,
        OrderDataProvider $orderDataProvider,
        CompanyManagement $companyManagement
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->customerRepository = $customerRepository;
        $this->urlBuilder = $urlBuilder;
        $this->localeDate = $localeDate;
        $this->quoteSession = $quoteSession;
        $this->statusSource = $statusSource;
        $this->expirationCalculator = $expirationCalculator;
        $this->quoteFactory = $quoteFactory;
        $this->adminUserSource = $adminUserSource;
        $this->config = $config;
        $this->groupRepository = $groupRepository;
        $this->orderDataProvider = $orderDataProvider;
        $this->companyManagement = $companyManagement;
    }

    /**
     * Get quote model
     *
     * @param int $quoteId
     * @return QuoteInterface
     * @throws NoSuchEntityException
     */
    public function getQuote($quoteId)
    {
        return $quoteId ? $this->quoteRepository->get($quoteId) : $this->quoteFactory->create();
    }

    /**
     * Get customer name
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCustomerName()
    {
        $customerName = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            } catch (NoSuchEntityException $e) {
            }
        }

        return $customerName;
    }

    /**
     * Get company name
     *
     * @return string
     */
    public function getCompanyName()
    {
        $companyName = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            $company = $this->companyManagement->getCompanyByCustomerId($customerId);
            $companyName = $company ? $company->getName() : '';
        }

        return $companyName;
    }

    /**
     * Retrieve formatted order id
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderIdFormatted($orderId)
    {
        return '#' . $this->orderDataProvider->getOrderIncrementId($orderId);
    }

    /**
     * Retrieve order url
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderUrl($orderId)
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Get customer group
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCustomerGroup()
    {
        $customerGroup = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $groupId = $customer->getGroupId();
                $customerGroup = $this->groupRepository->getById($groupId)->getCode();
            } catch (NoSuchEntityException $e) {
            }
        }

        return $customerGroup;
    }

    /**
     * Get customer name
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCustomerEmail()
    {
        $customerEmail = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $customerEmail = $customer->getEmail();
            } catch (NoSuchEntityException $e) {
            }
        }

        return $customerEmail;
    }

    /**
     * Get link to customer edit form in backend
     *
     * @return string
     */
    public function getLinkToCustomerEditForm()
    {
        $link = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            $link = $this->urlBuilder->getUrl('customer/index/edit', ['id' => $customerId]);
        }

        return $link;
    }

    /**
     * Get link to company edit form in backend
     *
     * @return string
     */
    public function getLinkToCompanyEditForm()
    {
        $link = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            $company = $this->companyManagement->getCompanyByCustomerId($customerId);
            if ($company) {
                $link = $this->urlBuilder->getUrl('aw_ca/company/edit', ['id' => $company->getId()]);
            }
        }

        return $link;
    }

    /**
     * Retrieve short date format
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->localeDate->getDateFormat();
    }

    /**
     * Prepare expiration date
     *
     * @param QuoteInterface $quote
     * @return string
     * @throws \Exception
     */
    public function prepareExpirationDate($quote)
    {
        return $quote->getId()
            ? $quote->getExpirationDate()
            : $this->expirationCalculator->calculateExpirationDate($this->quoteSession->getStoreId());
    }

    /**
     * Prepare quote status
     *
     * @param string $statusCode
     * @return string
     */
    public function prepareQuoteStatus($statusCode)
    {
        $options = $this->statusSource->getOptions();
        return $options[$statusCode] ?? '';
    }

    /**
     * Get list of admin users
     *
     * @return array
     */
    public function getListOfAdminUsers()
    {
        return $this->adminUserSource->toOptionArray();
    }

    /**
     * Is admin user select options is chosen
     *
     * @param array $adminUser
     * @param QuoteInterface $quote
     * @return bool
     */
    public function isAdminUserOptionSelected($adminUser, $quote)
    {
        $quoteSellerId = $quote->getSellerId();
        if ($quoteSellerId) {
            return $adminUser['value'] == $quoteSellerId;
        }

        return $adminUser['value'] == $this->config->getQuoteAssignedAdminUser();
    }
}
