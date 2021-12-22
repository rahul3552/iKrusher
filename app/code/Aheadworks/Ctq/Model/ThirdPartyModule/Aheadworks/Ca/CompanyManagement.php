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
namespace Aheadworks\Ctq\Model\ThirdPartyModule\Aheadworks\Ca;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;

/**
 * Class CompanyManagement
 * @package Aheadworks\Ctq\Model\ThirdPartyModule\Aheadworks\Ca
 */
class CompanyManagement
{
    /**
     * @var SellerCompanyManagementInterface|null
     */
    private $sellerCompanyManagement;

    /**
     * @param SellerCompanyManagementFactory $sellerCompanyManagementFactory
     */
    public function __construct(
        SellerCompanyManagementFactory $sellerCompanyManagementFactory
    ) {
        $this->sellerCompanyManagement = $sellerCompanyManagementFactory->create();
    }

    /**
     * Get company by customer id
     *
     * @param int $customerId
     * @return CompanyInterface|null
     */
    public function getCompanyByCustomerId($customerId)
    {
        return $this->sellerCompanyManagement
            ? $this->sellerCompanyManagement->getCompanyByCustomerId($customerId)
            : null;
    }
}
