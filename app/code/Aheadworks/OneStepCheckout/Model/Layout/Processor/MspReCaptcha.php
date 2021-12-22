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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\MspReCaptcha as MspReCaptchaStatus;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class MspReCaptcha
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class MspReCaptcha implements LayoutProcessorInterface
{
    /**
     * @var MspReCaptchaStatus
     */
    private $mspReCaptchaStatus;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param MspReCaptchaStatus $mspReCaptchaStatus
     * @param ObjectManagerInterface $objectManager
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        MspReCaptchaStatus $mspReCaptchaStatus,
        ObjectManagerInterface $objectManager,
        ArrayManager $arrayManager
    ) {
        $this->mspReCaptchaStatus = $mspReCaptchaStatus;
        $this->objectManager = $objectManager;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->mspReCaptchaStatus->isEnabled()) {
            $paths = [
                'components/checkout/children/authentication/children/msp_recaptcha' =>
                    'components/checkout/children/authentication/children/msp_recaptcha',
                'components/checkout/children/email/children/msp_recaptcha' =>
                    'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/'
                    . 'customer-email/children/msp_recaptcha'
            ];
            $nativeJsLayout = $this->getMspReCaptchaLayoutProcessor()->process([]);

            foreach ($paths as $pathToOur => $pathFromNative) {
                $ourLayout = $this->arrayManager->get($pathToOur, $jsLayout);
                $nativeLayout = $this->arrayManager->get($pathFromNative, $nativeJsLayout);
                if ($ourLayout && $nativeLayout) {
                    $jsLayout = $this->arrayManager->merge($pathToOur, $jsLayout, $nativeLayout);
                } elseif ($ourLayout && !$nativeLayout) {
                    $jsLayout = $this->arrayManager->remove($pathToOur, $jsLayout);
                }
            }
        }
        return $jsLayout;
    }

    /**
     * Retrieve MSP ReCaptcha layout processor
     *
     * @return \MSP\ReCaptcha\Block\LayoutProcessor\Checkout\Onepage
     */
    private function getMspReCaptchaLayoutProcessor()
    {
        return $this->objectManager->get(\MSP\ReCaptcha\Block\LayoutProcessor\Checkout\Onepage::class);
    }
}
