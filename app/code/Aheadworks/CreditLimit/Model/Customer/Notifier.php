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
namespace Aheadworks\CreditLimit\Model\Customer;

use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Email\Sender;
use Aheadworks\CreditLimit\Model\Customer\Notifier\ProcessorPool;
use Aheadworks\CreditLimit\Model\Config;

/**
 * Class Notifier
 *
 * @package Aheadworks\CreditLimit\Model\Customer
 */
class Notifier
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProcessorPool
     */
    private $processorPool;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Sender $sender
     * @param LoggerInterface $logger
     * @param ProcessorPool $processorPool
     */
    public function __construct(
        Sender $sender,
        LoggerInterface $logger,
        ProcessorPool $processorPool
    ) {
        $this->sender = $sender;
        $this->logger = $logger;
        $this->processorPool = $processorPool;
    }

    /**
     * Notify customer
     *
     * @param int $customerId
     * @param TransactionInterface $transaction
     * @return bool
     */
    public function notify($customerId, $transaction)
    {
        $processor = $this->processorPool->get($transaction->getAction());
        if (!$processor) {
            return false;
        }

        $emailMetadata = $processor->process($customerId, $transaction);
        if (!$emailMetadata) {
            return false;
        }

        try {
            $this->sender->send($emailMetadata);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }

        return true;
    }
}
