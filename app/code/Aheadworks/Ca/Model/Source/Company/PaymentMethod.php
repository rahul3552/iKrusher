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
namespace Aheadworks\Ca\Model\Source\Company;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Payment\Model\MethodInterface;

/**
 * Class PaymentMethod
 * @package Aheadworks\Ca\Model\Source\Company
 */
class PaymentMethod implements OptionSourceInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Manager
     */
    protected $thirdPartyModuleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Manager $thirdPartyModuleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $thirdPartyModuleManager
    ) {
        $this->objectManager = $objectManager;
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->thirdPartyModuleManager->isAwPayRestModuleEnabled()) {
            $paymentGroups = $this->getPaymentManagement()->getPaymentMethodsByGroups();
            foreach ($paymentGroups as $paymentGroup) {
                $payments = $paymentGroup['payments'];
                if (!$payments) {
                    continue;
                }
                if ($subOptions = $this->getPaymentOptions($payments)) {
                    $options[] = [
                        'label' => $paymentGroup['label'],
                        'value' => $subOptions
                    ];
                }
            }
        }

        return $options;
    }

    /**
     * Retrieve payment options
     *
     * @param MethodInterface[] $payments
     * @return array
     */
    public function getPaymentOptions($payments)
    {
        $options = [];
        foreach ($payments as $payment) {
            if ($payment->isActive()) {
                $options[] = [
                    'label' => $payment->getTitle() . ' (' . $payment->getCode() . ')',
                    'value' => $payment->getCode()
                ];
            }
        }
        return $options;
    }

    /**
     * Retrieve Payment Restriction Payment Management
     *
     * @return \Aheadworks\PaymentRestrictions\Model\PaymentManagement
     */
    protected function getPaymentManagement()
    {
        return $this->objectManager->get(\Aheadworks\PaymentRestrictions\Model\PaymentManagement::class);
    }
}
