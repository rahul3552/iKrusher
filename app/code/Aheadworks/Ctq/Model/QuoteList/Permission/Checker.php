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
namespace Aheadworks\Ctq\Model\QuoteList\Permission;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class Checker
 * @package Aheadworks\Ctq\Model\QuoteList\Permission
 */
class Checker
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var BuyerPermissionManagementInterface
     */
    private $buyerPermissionManagement;

    /**
     * @param HttpContext $httpContext
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     */
    public function __construct(
        HttpContext $httpContext,
        BuyerPermissionManagementInterface $buyerPermissionManagement
    ) {
        $this->httpContext = $httpContext;
        $this->buyerPermissionManagement = $buyerPermissionManagement;
    }

    /**
     * Check is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        $customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        $companyInfo = $this->httpContext->getValue('company_info');
        $isAllowedToQuote = $companyInfo['is_allowed_to_quote'] ?? null;
        $result = $this->buyerPermissionManagement->isAllowQuoteList($customerGroupId, null);

        if ($isAllowedToQuote !== null) {
            $result = $result && $isAllowedToQuote;
        }

        return $result;
    }
}
