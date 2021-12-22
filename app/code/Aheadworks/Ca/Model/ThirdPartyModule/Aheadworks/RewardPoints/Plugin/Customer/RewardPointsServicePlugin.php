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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\SummaryManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

/**
 * Class RewardPointsServicePlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class RewardPointsServicePlugin
{
    /**
     * @var RewardPointsManagement
     */
    private $rewardPointsManagement;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var SummaryManagement
     */
    private $summaryManagement;

    /**
     * @param RewardPointsManagement $rewardPointsManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param SummaryManagement $summaryManagement
     */
    public function __construct(
        RewardPointsManagement $rewardPointsManagement,
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        SummaryManagement $summaryManagement
    ) {
        $this->rewardPointsManagement = $rewardPointsManagement;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->summaryManagement = $summaryManagement;
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $customerId
     * @param int|null $websiteId
     * @return array
     */
    public function beforeGetCustomerRewardPointsDetails($subject, $customerId, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $websiteId];
    }

    /**
     * Adjust customer reward points details
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param \Aheadworks\RewardPoints\Api\Data\CustomerRewardPointsDetailsInterface $resultDetails
     * @param int $customerId
     * @param int|null $websiteId
     * @return \Aheadworks\RewardPoints\Api\Data\CustomerRewardPointsDetailsInterface
     * @throws NoSuchEntityException
     */
    public function afterGetCustomerRewardPointsDetails($subject, $resultDetails, $customerId, $websiteId = null)
    {
        $resultDetails = $this->rewardPointsManagement->adjustRewardPointsDetails(
            $resultDetails,
            $customerId,
            $websiteId
        );
        return $resultDetails;
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $orderId
     * @param int|null $customerId
     * @return array
     */
    public function beforeSpendPointsOnCheckout($subject, $orderId, $customerId = null)
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $order = null;
        }
        $customerId = $order
            ? $this->rewardPointsManagement->changeCustomerIdIfNeeded($order->getCustomerId())
            : $customerId;

        return [$orderId, $customerId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $orderId
     * @param int|null $customerId
     * @return array
     */
    public function beforeReimbursedSpentRewardPointsOrderCancel($subject, $orderId, $customerId = null)
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $order = null;
        }
        $customerId = $order
            ? $this->rewardPointsManagement->changeCustomerIdIfNeeded($order->getCustomerId())
            : $customerId;

        return [$orderId, $customerId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $invoiceId
     * @param int|null $customerId
     * @return array
     */
    public function beforeAddPointsForPurchases($subject, $invoiceId, $customerId = null)
    {
        try {
            $invoice = $this->invoiceRepository->get($invoiceId);
            $order = $this->orderRepository->get($invoice->getOrderId());
        } catch (NoSuchEntityException $e) {
            $order = null;
        }
        $customerId = $order
            ? $this->rewardPointsManagement->changeCustomerIdIfNeeded($order->getCustomerId())
            : $customerId;

        return [$invoiceId, $customerId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $creditmemoId
     * @param int|null $customerId
     * @return array
     */
    public function beforeRefundToRewardPoints($subject, $creditmemoId, $customerId = null)
    {
        $newCustomerId = $this->resolveCustomerIdByCreditmemo($creditmemoId);
        $customerId = $newCustomerId ?? $customerId;

        return [$creditmemoId, $customerId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $creditmemoId
     * @param int|null $customerId
     * @return array
     */
    public function beforeReimbursedSpentRewardPoints($subject, $creditmemoId, $customerId = null)
    {
        $newCustomerId = $this->resolveCustomerIdByCreditmemo($creditmemoId);
        $customerId = $newCustomerId ?? $customerId;

        return [$creditmemoId, $customerId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $creditmemoId
     * @param int|null $customerId
     * @return array
     */
    public function beforeCancelEarnedPointsRefundOrder($subject, $creditmemoId, $customerId = null)
    {
        $newCustomerId = $this->resolveCustomerIdByCreditmemo($creditmemoId);
        $customerId = $newCustomerId ?? $customerId;

        return [$creditmemoId, $customerId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $customerId
     * @param int|null $websiteId
     * @return array
     */
    public function beforeAddPointsForRegistration($subject, $customerId, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $websiteId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $customerId
     * @param boolean $isOwner
     * @param int|null $websiteId
     * @return array
     */
    public function beforeAddPointsForReviews($subject, $customerId, $isOwner, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $isOwner, $websiteId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $customerId
     * @param int $productId
     * @param string $shareNetwork
     * @param int|null $websiteId
     * @return array
     */
    public function beforeAddPointsForShares($subject, $customerId, $productId, $shareNetwork, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $productId, $shareNetwork, $websiteId];
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param int $customerId
     * @param int|null $websiteId
     * @return array
     */
    public function beforeAddPointsForNewsletterSignup($subject, $customerId, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $websiteId];
    }

    /**
     * Resolve customer ID by creditmemo
     *
     * @param int $creditmemoId
     * @return int
     */
    private function resolveCustomerIdByCreditmemo($creditmemoId)
    {
        try {
            $creditmemo = $this->creditmemoRepository->get($creditmemoId);
            $order = $this->orderRepository->get($creditmemo->getOrderId());
            $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($order->getCustomerId());
        } catch (NoSuchEntityException $e) {
            $customerId = null;
        }

        return $customerId;
    }

    /**
     * Change customer ID to company admin ID if required
     *
     * @param \Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface $subject
     * @param array $transactionData
     * @return array
     */
    public function beforeSaveAdminTransaction($subject, $transactionData)
    {
        $customerId = $transactionData['customer_id'];
        $rootCustomer = $this->rewardPointsManagement->getRootUserByCustomerId($customerId);
        if ($rootCustomer) {
            $transactionData['customer_id'] = $rootCustomer->getId();
            $transactionData['customer_name'] = $rootCustomer->getFirstname() . ' ' . $rootCustomer->getLastname();
            $transactionData['customer_email'] = $rootCustomer->getEmail();
        }
        return [$transactionData];
    }

    /**
     * Retrieve root customer balance
     *
     * @param CustomerRewardPointsService $subject
     * @param int $result
     * @param int $customerId
     * @return int
     */
    public function afterGetCustomerRewardPointsBalanceForTransaction($subject, $result, $customerId)
    {
        return $this->summaryManagement->getCustomerPointsBalance($customerId);
    }
}
