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
namespace Aheadworks\CreditLimit\Model\Customer\Notifier\EmailProcessor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Framework\App\Area;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\CreditLimit\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\CreditLimit\Model\Email\EmailMetadataInterface;
use Aheadworks\CreditLimit\Model\Email\VariableProcessorInterface;
use Aheadworks\CreditLimit\Model\Customer\Notifier\EmailProcessorInterface;
use Aheadworks\CreditLimit\Model\Config;
use Aheadworks\CreditLimit\Model\Source\Customer\EmailVariables;

/**
 * Class CreditBalanceUpdated
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier\EmailProcessor
 */
class CreditBalanceUpdated implements EmailProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var VariableProcessorInterface
     */
    private $variableProcessorComposite;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param Config $config
     * @param CustomerRepositoryInterface $customerRepository
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorInterface $variableProcessorComposite
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        Config $config,
        CustomerRepositoryInterface $customerRepository,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorInterface $variableProcessorComposite,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function process($customerId, $transaction)
    {
        $customer = $this->customerRepository->getById($customerId);
        $storeId = $customer->getStoreId();

        if (!$this->config->isAllowedToSendEmailOnBalanceUpdate($storeId)) {
            return false;
        }

        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($customer, $transaction))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($customer))
            ->setRecipientEmail($this->getRecipientEmail($customer));

        return $emailMetaData;
    }

    /**
     * Retrieve template ID
     *
     * @param int $storeId
     * @return string
     */
    private function getTemplateId($storeId)
    {
        return $this->config->getCreditBalanceUpdatedTemplate($storeId);
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    private function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param CustomerInterface $customer
     * @param TransactionInterface $transaction
     * @return array
     */
    private function prepareTemplateVariables($customer, $transaction)
    {
        $templateVariables = [
            EmailVariables::CUSTOMER => $this->dataObjectProcessor->buildOutputDataArray(
                $customer,
                CustomerInterface::class
            ),
            EmailVariables::TRANSACTION => $this->dataObjectProcessor->buildOutputDataArray(
                $transaction,
                TransactionInterface::class
            ),
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderName($storeId)
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderEmail($storeId)
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Retrieve recipient name
     *
     * @param CustomerInterface $customer
     * @return string
     */
    private function getRecipientName($customer)
    {
        return $customer->getFirstname() . ' ' . $customer->getLastname();
    }

    /**
     * Retrieve recipient email
     *
     * @param CustomerInterface $customer
     * @return string
     */
    private function getRecipientEmail($customer)
    {
        return $customer->getEmail();
    }
}
