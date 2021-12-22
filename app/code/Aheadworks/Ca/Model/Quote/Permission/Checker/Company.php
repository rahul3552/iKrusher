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
namespace Aheadworks\Ca\Model\Quote\Permission\Checker;

use Aheadworks\Ca\Api\SellerCompanyManagementInterface;

/**
 * Class Company
 * @package Aheadworks\Ca\Model\Quote\Permission\Checker
 */
class Company
{
    /**
     * @var SellerCompanyManagementInterface
     */
    private $sellerCompanyManagement;

    /**
     * @param SellerCompanyManagementInterface $sellerCompanyManagement
     */
    public function __construct(
        SellerCompanyManagementInterface $sellerCompanyManagement
    ) {
        $this->sellerCompanyManagement = $sellerCompanyManagement;
    }

    /**
     * Check allow customer to quote by company
     *
     * @param int $customerId
     * @param int $storeId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function check($customerId, $storeId)
    {
        $company = $this->sellerCompanyManagement->getCompanyByCustomerId($customerId);
        return $company ? $company->getIsAllowedToQuote() : true;
    }
}
