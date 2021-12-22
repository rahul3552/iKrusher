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

use Aheadworks\OneStepCheckout\Model\Layout\DefinitionFetcher;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class DefaultProcessor
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class DefaultProcessor implements LayoutProcessorInterface
{
    /**
     * @var DefinitionFetcher
     */
    private $definitionFetcher;

    /**
     * @param DefinitionFetcher $definitionFetcher
     */
    public function __construct(DefinitionFetcher $definitionFetcher)
    {
        $this->definitionFetcher = $definitionFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if (isset($jsLayout['components']['checkout']['children']['checkoutConfig']
            ['children']['shipping-rates-validation']['children'])
        ) {
            $this->addShippingRatesValidators(
                $jsLayout['components']['checkout']['children']['checkoutConfig']
                ['children']['shipping-rates-validation']['children']
            );
        }
        if (isset($jsLayout['components']['checkout']['children']['checkoutConfig']
            ['children']['payment-renders']['children'])
        ) {
            $this->addPaymentMethodsRenders(
                $jsLayout['components']['checkout']['children']['checkoutConfig']
                ['children']['payment-renders']['children']
            );
            if (isset($jsLayout['components']['checkout']['children']['paymentMethod']
                ['children']['billingAddress'])
            ) {
                $this->configureBillingAddressComponent(
                    $jsLayout['components']['checkout']['children']['checkoutConfig']
                    ['children']['payment-renders']['children'],
                    $jsLayout['components']['checkout']['children']['paymentMethod']['children']['billingAddress']
                );
            }
        }
        if (isset($jsLayout['components']['checkout']['children']['authentication']['children'])) {
            $this->addAdditionalAuthenticationFormFields(
                $jsLayout['components']['checkout']['children']['authentication']['children']
            );
        }
        if (isset($jsLayout['components']['checkout']['children']['email']['children'])) {
            $this->addChildrenLoginFormFields(
                $jsLayout['components']['checkout']['children']['email']['children']
            );
        }
        return $jsLayout;
    }

    /**
     * Add shipping rates validators definitions
     *
     * @param array $layout
     * @return void
     */
    private function addShippingRatesValidators(array &$layout)
    {
        $path = '//referenceBlock[@name="checkout.root"]/arguments/argument[@name="jsLayout"]'
            . '/item[@name="components"]/item[@name="checkout"]/item[@name="children"]'
            . '/item[@name="steps"]/item[@name="children"]/item[@name="shipping-step"]'
            . '/item[@name="children"]/item[@name="step-config"]/item[@name="children"]'
            . '/item[@name="shipping-rates-validation"]/item[@name="children"]';
        $definitions = $this->definitionFetcher->fetchArgs('checkout_index_index', $path);
        $layout = $definitions;
    }

    /**
     * Add payment methods renders definitions
     *
     * @param array $layout
     * @return void
     */
    private function addPaymentMethodsRenders(array &$layout)
    {
        $path = '//referenceBlock[@name="checkout.root"]/arguments/argument[@name="jsLayout"]'
            . '/item[@name="components"]/item[@name="checkout"]/item[@name="children"]'
            . '/item[@name="steps"]/item[@name="children"]/item[@name="billing-step"]'
            . '/item[@name="children"]/item[@name="payment"]/item[@name="children"]'
            . '/item[@name="renders"]/item[@name="children"]';
        $definitions = $this->definitionFetcher->fetchArgs('checkout_index_index', $path);
        $layout = $definitions;
    }

    /**
     * Configure billing address component
     *
     * @param array $paymentLayout
     * @param array $billingAddressLayout
     * @return void
     */
    private function configureBillingAddressComponent(array $paymentLayout, array &$billingAddressLayout)
    {
        if (!isset($billingAddressLayout['config'])) {
            $billingAddressLayout['config'] = [];
        }

        $config = &$billingAddressLayout['config'];
        $availableForMethods = [];
        foreach ($paymentLayout as $groupConfig) {
            foreach ($groupConfig['methods'] as $methodCode => $methodConfig) {
                if (isset($methodConfig['isBillingAddressRequired'])
                    && $methodConfig['isBillingAddressRequired']
                ) {
                    $availableForMethods[] = $methodCode;
                }
            }
        }
        $config['availableForMethods'] = $availableForMethods;
    }

    /**
     * Add additional authentication form fields
     *
     * @param array $layout
     * @return void
     */
    private function addAdditionalAuthenticationFormFields(array &$layout)
    {
        $path = '//referenceBlock[@name="checkout.root"]/arguments/argument[@name="jsLayout"]'
            . '/item[@name="components"]/item[@name="checkout"]/item[@name="children"]'
            . '/item[@name="authentication"]/item[@name="children"]';
        $definitions = $this->definitionFetcher->fetchArgs('checkout_index_index', $path);
        $layout = array_merge($layout, $definitions);
    }

    /**
     * Add additional login form fields
     *
     * @param array $layout
     * @return void
     */
    private function addChildrenLoginFormFields(array &$layout)
    {
        $path = '//referenceBlock[@name="checkout.root"]/arguments/argument[@name="jsLayout"]'
            . '/item[@name="components"]/item[@name="checkout"]/item[@name="children"]'
            . '/item[@name="steps"]/item[@name="children"]/item[@name="shipping-step"]'
            . '/item[@name="children"]/item[@name="shippingAddress"]/item[@name="children"]'
            . '/item[@name="customer-email"]/item[@name="children"]';
        $definitions = $this->definitionFetcher->fetchArgs('checkout_index_index', $path);
        $layout = array_merge_recursive($layout, $definitions);
    }
}
