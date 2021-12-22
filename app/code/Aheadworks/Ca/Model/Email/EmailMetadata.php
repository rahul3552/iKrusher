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
namespace Aheadworks\Ca\Model\Email;

use Magento\Framework\DataObject;

/**
 * Class EmailMetadata
 * @package Aheadworks\Ca\Model\Email
 */
class EmailMetadata extends DataObject implements EmailMetadataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateId($templateId)
    {
        return $this->setData(self::TEMPLATE_ID, $templateId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateOptions()
    {
        return $this->getData(self::TEMPLATE_OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateOptions($templateOptions)
    {
        return $this->setData(self::TEMPLATE_OPTIONS, $templateOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVariables()
    {
        return $this->getData(self::TEMPLATE_VARIABLES);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateVariables($templateVariables)
    {
        return $this->setData(self::TEMPLATE_VARIABLES, $templateVariables);
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName()
    {
        return $this->getData(self::SENDER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderName($senderName)
    {
        return $this->setData(self::SENDER_NAME, $senderName);
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderEmail()
    {
        return $this->getData(self::SENDER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderEmail($senderEmail)
    {
        return $this->setData(self::SENDER_EMAIL, $senderEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientName()
    {
        return $this->getData(self::RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientName($senderName)
    {
        return $this->setData(self::RECIPIENT_NAME, $senderName);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail($senderEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $senderEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getCc()
    {
        return $this->getData(self::CC);
    }

    /**
     * {@inheritdoc}
     */
    public function setCc($addresses)
    {
        return $this->setData(self::CC, $addresses);
    }

    /**
     * @inheritDoc
     */
    public function getBcc()
    {
        return $this->getData(self::BCC);
    }

    /**
     * @inheritDoc
     */
    public function setBcc($addresses)
    {
        return $this->setData(self::BCC, $addresses);
    }
}
