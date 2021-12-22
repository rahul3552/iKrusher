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
namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\CompanyInterface;

/**
 * Class CompanyResource
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class Company extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_ca_company';

    const COMPANY_PAYMENTS_TABLE_NAME = 'aw_ca_company_payments';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, CompanyInterface::ID);
    }
}
