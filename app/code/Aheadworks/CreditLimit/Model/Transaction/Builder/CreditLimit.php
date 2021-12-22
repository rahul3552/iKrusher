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
namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Calculator as BalanceCalculator;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Service\CustomerGroupService;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CreditLimit
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class CreditLimit extends AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @var BalanceCalculator
     */
    private $balanceCalculator;

    /**
     * @var CustomerGroupService
     */
    private $customerGroupConfig;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param TransactionActionSource $transactionActionSource
     * @param CreditSummaryManagement $summaryManagement
     * @param BalanceCalculator $balanceCalculator
     * @param CustomerGroupService $customerGroupConfig
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        TransactionActionSource $transactionActionSource,
        CreditSummaryManagement $summaryManagement,
        BalanceCalculator $balanceCalculator,
        CustomerGroupService $customerGroupConfig,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($transactionActionSource, $summaryManagement);
        $this->balanceCalculator = $balanceCalculator;
        $this->customerGroupConfig = $customerGroupConfig;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        $updateCreditLimitActions = $this->transactionActionSource->getActionsToUpdateCreditLimit();
        if (!in_array($params->getAction(), $updateCreditLimitActions)) {
            return false;
        }

        $summary = $this->summaryManagement->getCreditSummary($params->getCustomerId());
        if ($params->getIsCustomCreditLimit()) {
            if (!$summary->getSummaryId() && $params->getCreditLimit() == 0) {
                throw new LocalizedException(__('Zero amount cannot be assigned as credit limit'));
            }
        }

        if (!is_numeric($params->getCreditLimit())) {
            throw new LocalizedException(__('Credit limit value is not correct'));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $creditLimit = $params->getCreditLimit();

        $customer = $this->customerRepository->getById($params->getCustomerId());
        $summary = $this->summaryManagement->getCreditSummary($params->getCustomerId());
        $summary->setCreditLimit($creditLimit);

        if (!$params->getIsCustomCreditLimit()) {
            $creditLimit = $this->customerGroupConfig->getCreditLimit(
                $customer->getGroupId(),
                $customer->getWebsiteId()
            );
            $summary->setCreditLimit(null);
        } else {
            $summary->setCreditLimit($creditLimit);
        }

        $availableCredit = $this->balanceCalculator->calculateAvailableCredit(
            $summary->getCreditBalance(),
            $creditLimit
        );

        if (!$summary->getSummaryId()) {
            $transaction->setAction(TransactionActionSource::CREDIT_LIMIT_ASSIGNED);
        }

        $summary = $this->summaryManagement->saveCreditSummary($summary);

        $transaction->setCreditBalance($summary->getCreditBalance());
        $transaction->setCreditAvailable($availableCredit);
        $transaction->setCreditLimit($creditLimit);
        $transaction->setCreditCurrency($summary->getCurrency());
        $transaction->setActionCurrency($summary->getCurrency());
        $transaction->setSummaryId($summary->getSummaryId());
    }
}
