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
namespace Aheadworks\Ctq\Model\Quote\Expiration;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\Sender;
use Aheadworks\Ctq\Model\Quote\Expiration\Notifier\Processor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface;

/**
 * Class Notifier
 *
 * @package Aheadworks\Ctq\Model\Quote\Expiration
 */
class Notifier
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var Processor
     */
    private $emailProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Sender $sender
     * @param Processor $emailProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Sender $sender,
        Processor $emailProcessor,
        LoggerInterface $logger
    ) {
        $this->sender = $sender;
        $this->emailProcessor = $emailProcessor;
        $this->logger = $logger;
    }

    /**
     * Notify about soon quote expiration
     *
     * @param QuoteInterface $quote
     * @return bool
     * @throws LocalizedException
     */
    public function notify($quote)
    {
        $emailMetadata = $this->emailProcessor->process($quote);
        try {
            $this->sender->send($emailMetadata);
        } catch (MailException $e) {
            $this->logger->critical($e);
            return false;
        }
        return true;
    }
}
