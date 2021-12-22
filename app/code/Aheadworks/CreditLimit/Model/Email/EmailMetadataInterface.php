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
namespace Aheadworks\CreditLimit\Model\Email;

/**
 * Interface EmailMetadataInterface
 *
 * @package Aheadworks\CreditLimit\Model\Email
 */
interface EmailMetadataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const TEMPLATE_ID = 'template_id';
    const TEMPLATE_OPTIONS = 'template_options';
    const TEMPLATE_VARIABLES = 'template_variables';
    const SENDER_NAME = 'sender_name';
    const SENDER_EMAIL = 'sender_email';
    const RECIPIENT_NAME = 'recipient_name';
    const RECIPIENT_EMAIL = 'recipient_email';
    /**#@-*/

    /**
     * Get template ID
     *
     * @return string
     */
    public function getTemplateId();

    /**
     * Set template ID
     *
     * @param string $templateId
     * @return $this
     */
    public function setTemplateId($templateId);

    /**
     * Get template options
     *
     * @return array
     */
    public function getTemplateOptions();

    /**
     * Set template options
     *
     * @param array $templateOptions
     * @return $this
     */
    public function setTemplateOptions($templateOptions);

    /**
     * Get template variables
     *
     * @return array
     */
    public function getTemplateVariables();

    /**
     * Set template variables
     *
     * @param array $templateVariables
     * @return $this
     */
    public function setTemplateVariables($templateVariables);

    /**
     * Get sender name
     *
     * @return string
     */
    public function getSenderName();

    /**
     * Set sender name
     *
     * @param string $senderName
     * @return $this
     */
    public function setSenderName($senderName);

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail();

    /**
     * Set sender email
     *
     * @param string $senderEmail
     * @return $this
     */
    public function setSenderEmail($senderEmail);

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * Set recipient name
     *
     * @param string $recipientName
     * @return $this
     */
    public function setRecipientName($recipientName);

    /**
     * Get recipient email
     *
     * @return string|array
     */
    public function getRecipientEmail();

    /**
     * Set recipient email
     *
     * @param string|array $recipientEmail
     * @return $this
     */
    public function setRecipientEmail($recipientEmail);
}
