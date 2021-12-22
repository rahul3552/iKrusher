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
namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

/**
 * Class NewCompanyApproved
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
class NewCompanyApproved extends AbstractProcessor implements EmailProcessorInterface
{
    /**
     * @inheritdoc
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getNewCompanyApprovedTemplate($storeId);
    }

    /**
     * @inheritdoc
     */
    protected function getRecipientName($company)
    {
        $customer = $this->getRootCustomer($company);
        return $customer->getFirstname() . ' ' .  $customer->getLastname();
    }

    /**
     * @inheritdoc
     */
    protected function getRecipientEmail($company)
    {
        $customer = $this->getRootCustomer($company);
        return $customer->getEmail();
    }
}
