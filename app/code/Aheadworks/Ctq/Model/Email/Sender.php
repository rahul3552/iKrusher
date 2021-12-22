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
namespace Aheadworks\Ctq\Model\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderFactory;

/**
 * Class Sender
 * @package Aheadworks\Ctq\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilderFactory
     */
    private $transportBuilderFactory;

    /**
     * @param TransportBuilderFactory $transportBuilderFactory
     */
    public function __construct(
        TransportBuilderFactory $transportBuilderFactory
    ) {
        $this->transportBuilderFactory = $transportBuilderFactory;
    }

    /**
     * Send email message
     *
     * @param EmailMetadataInterface $emailMetadata
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send($emailMetadata)
    {
        /** @var TransportBuilder $transportBuilder */
        $transportBuilder = $this->transportBuilderFactory->create();

        $transportBuilder
            ->setTemplateIdentifier($emailMetadata->getTemplateId())
            ->setTemplateOptions($emailMetadata->getTemplateOptions())
            ->setTemplateVars($emailMetadata->getTemplateVariables())
            ->setFrom(['name' => $emailMetadata->getSenderName(), 'email' => $emailMetadata->getSenderEmail()])
            ->addTo($emailMetadata->getRecipientEmail(), $emailMetadata->getRecipientName());

        if ($emailMetadata->getCc()) {
            $transportBuilder->addCc($emailMetadata->getCc());
        }
        if ($emailMetadata->getBcc()) {
            $transportBuilder->addBcc($emailMetadata->getBcc());
        }

        $transportBuilder
            ->getTransport()
            ->sendMessage();
    }
}
