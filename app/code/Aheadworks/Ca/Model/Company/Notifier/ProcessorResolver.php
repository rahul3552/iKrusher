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
namespace Aheadworks\Ca\Model\Company\Notifier;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Source\Company\Status;
use Aheadworks\Ca\Model\Company\Notifier;

/**
 * Class ProcessorResolver
 *
 * @package Aheadworks\Ca\Model\Company\Notifier
 */
class ProcessorResolver
{
    /**
     * Resolve notify processor
     *
     * @param CompanyInterface $company
     * @param string|null $oldStatus
     * @return string
     */
    public function resolve($company, $oldStatus = null)
    {
        $processorName = '';
        if (!$company->getIsApprovedNotificationSent() && $company->getStatus() == Status::APPROVED) {
            $processorName = Notifier::NEW_COMPANY_APPROVED_PROCESSOR;
            $company->setIsApprovedNotificationSent(true);
            $company->setIsDeclinedNotificationSent(true);
            return $processorName;
        }
        if (!$company->getIsDeclinedNotificationSent() && $company->getStatus() == Status::DECLINED) {
            $processorName = Notifier::NEW_COMPANY_DECLINED_PROCESSOR;
            $company->setIsDeclinedNotificationSent(true);
            return $processorName;
        }

        if ($oldStatus && $oldStatus != $company->getStatus()) {
            $processorName = Notifier::COMPANY_STATUS_CHANGED_PROCESSOR;
        }

        return $processorName;
    }
}
