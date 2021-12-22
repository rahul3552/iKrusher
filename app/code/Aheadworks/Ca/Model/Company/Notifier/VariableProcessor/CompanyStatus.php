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
namespace Aheadworks\Ca\Model\Company\Notifier\VariableProcessor;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Source\Company\Status;

/**
 * Class CompanyStatus
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\VariableProcessor
 */
class CompanyStatus implements VariableProcessorInterface
{
    /**
     * @var Status
     */
    private $companyStatus;

    /**
     * @param Status $companyStatus
     */
    public function __construct(Status $companyStatus)
    {
        $this->companyStatus = $companyStatus;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var CompanyInterface $company */
        $company = $variables[EmailVariables::COMPANY];
        $variables[EmailVariables::COMPANY_STATUS] = $this->companyStatus->getStatusLabel($company->getStatus());

        return $variables;
    }
}
