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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Plugin\Model;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Model\PaymentManagement;
use Aheadworks\PaymentRestrictions\Model\PaymentManagement as PayRestPaymentManagement;

/**
 * Class PaymentManagementPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Plugin\Model
 */
class PaymentManagementPlugin
{
    /**
     * @var PaymentManagement
     */
    private $paymentManagement;

    /**
     * @param PaymentManagement $paymentManagement
     */
    public function __construct(PaymentManagement $paymentManagement)
    {
        $this->paymentManagement = $paymentManagement;
    }

    /**
     * Is available by method code
     *
     * @param PayRestPaymentManagement $subject
     * @param \Closure $proceed
     * @param $paymentCode
     * @param int|null $group
     * @param int|null $websiteId
     * @return bool
     */
    public function aroundIsAvailable(
        PayRestPaymentManagement $subject,
        \Closure $proceed,
        $paymentCode,
        $group = null,
        $websiteId = null
    ) {
        $allowedPaymentMethods = $this->paymentManagement->getAllowedCompanyPaymentMethods();
        if (!empty($allowedPaymentMethods)) {
            $result = in_array($paymentCode, $allowedPaymentMethods);
        } else {
            $result = $proceed($paymentCode, $group, $websiteId);
        }

        return $result;
    }
}
