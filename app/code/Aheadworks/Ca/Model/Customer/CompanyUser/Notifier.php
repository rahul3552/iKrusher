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
namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Aheadworks\Ca\Model\Customer\CompanyUser\Notifier\EmailProcessorInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Sender;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Class Notifier
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser
 */
class Notifier
{
    /**#@+
     * Constants for processors
     */
    const NEW_COMPANY_USER_CREATED_PROCESSOR = 'new_company_user_created_processor';
    /**#@-*/

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EmailProcessorInterface[]
     */
    private $processors;

    /**
     * @param Sender $sender
     * @param LoggerInterface $logger
     * @param array $processors
     */
    public function __construct(
        Sender $sender,
        LoggerInterface $logger,
        $processors = []
    ) {
        $this->sender = $sender;
        $this->logger = $logger;
        $this->processors = $processors;
    }

    /**
     * Notify on new user created
     *
     * @param CustomerInterface $customer
     * @throws LocalizedException
     */
    public function notifyOnNewUserCreated($customer)
    {
        $emailMetadataObjects = $this->processors[self::NEW_COMPANY_USER_CREATED_PROCESSOR]->process($customer);
        $this->send($emailMetadataObjects);
    }

    /**
     * Send email
     *
     * @param EmailMetadataInterface[] $emailMetadataObjects
     * @throws LocalizedException
     */
    private function send($emailMetadataObjects)
    {
        /** @var EmailMetadataInterface $emailMetadata */
        foreach ($emailMetadataObjects as $emailMetadata) {
            try {
                if ($emailMetadata->getRecipientEmail()) {
                    $this->sender->send($emailMetadata);
                }
            } catch (MailException $e) {
                $this->logger->critical($e);
            }
        }
    }
}
