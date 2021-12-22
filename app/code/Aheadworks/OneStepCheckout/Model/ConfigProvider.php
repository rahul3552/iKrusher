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
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Model\Cart\ImageProvider;
use Aheadworks\OneStepCheckout\Model\Cart\OptionsProvider as ItemOptionsProvider;
use Aheadworks\OneStepCheckout\Model\ConfigProvider\DefaultShippingMethod;
use Aheadworks\OneStepCheckout\Model\ConfigProvider\PaymentMethodList;
use Aheadworks\OneStepCheckout\Model\DeliveryDate\ConfigProvider as DeliveryDateConfigProvider;
use Aheadworks\OneStepCheckout\Model\Newsletter\ConfigProvider as NewsletterConfigProvider;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;

/**
 * Class ConfigProvider
 * @package Aheadworks\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PaymentMethodList
     */
    private $paymentMethodsProvider;

    /**
     * @var NewsletterConfigProvider
     */
    private $subscriberConfigProvider;

    /**
     * @var DeliveryDateConfigProvider
     */
    private $deliveryDateConfigProvider;

    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @var ItemOptionsProvider
     */
    private $itemOptionsProvider;

    /**
     * @var DefaultShippingMethod
     */
    private $defaultShippingMethodProvider;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param CheckoutSession $checkoutSession
     * @param Config $config
     * @param PaymentMethodList $paymentMethodsProvider
     * @param NewsletterConfigProvider $subscriberConfigProvider
     * @param DeliveryDateConfigProvider $deliveryDateConfigProvider
     * @param ImageProvider $imageProvider
     * @param ItemOptionsProvider $itemOptionsProvider
     * @param DefaultShippingMethod $defaultShippingMethodProvider
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Config $config,
        PaymentMethodList $paymentMethodsProvider,
        NewsletterConfigProvider $subscriberConfigProvider,
        DeliveryDateConfigProvider $deliveryDateConfigProvider,
        ImageProvider $imageProvider,
        ItemOptionsProvider $itemOptionsProvider,
        DefaultShippingMethod $defaultShippingMethodProvider,
        UrlInterface $urlBuilder
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->paymentMethodsProvider = $paymentMethodsProvider;
        $this->subscriberConfigProvider = $subscriberConfigProvider;
        $this->deliveryDateConfigProvider = $deliveryDateConfigProvider;
        $this->imageProvider = $imageProvider;
        $this->itemOptionsProvider = $itemOptionsProvider;
        $this->defaultShippingMethodProvider = $defaultShippingMethodProvider;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $quoteId = $this->checkoutSession->getQuoteId();
        $quote = $this->checkoutSession->getQuote();
        $config = [
            'sameAsShippingFlag' => $this->config->isBillingShippingAreTheSame($quote->getStore()->getWebsiteId()),
            'paymentMethods' => $this->paymentMethodsProvider->getPaymentMethods($quoteId),
            'newsletterSubscribe' => $this->subscriberConfigProvider->getConfig(),
            'isOrderNoteEnabled' => $this->config->isOrderNoteEnabled(),
            'isMiniCartExpanded' => $this->config->isMiniCartExpanded(),
            'isEnabledCheckoutStatistics' => $this->config->isEnabledCheckoutStatistics(),
            'deliveryDate' => $this->deliveryDateConfigProvider->getConfig(),
            'editableItemOptions' => $this->itemOptionsProvider->getOptionsData($quoteId),
            'itemImageData' => $this->imageProvider->getConfigImageData($quoteId),
            'trustSeals' => [
                'isEnabled' => $this->config->isTrustSealsBlockEnabled(),
                'label' => $this->config->getTrustSealsLabel(),
                'text' => $this->config->getTrustSealsText(),
                'badges' => $this->config->getTrustSealsBadges()
            ],
            'defaultRedirectOnEmptyQuoteUrl' => $this->getDefaultRedirectOnEmptyQuoteUrl(),
            'googleAutocomplete' => [
                'apiKey' => $this->config->getGooglePlacesApiKey()
            ],
            'optionsPostUrl' => $this->urlBuilder->getUrl('onestepcheckout/index/optionspost')
        ];
        $defaultShippingMethod = $this->defaultShippingMethodProvider->getShippingMethod();
        if (!empty($defaultShippingMethod)) {
            $config['defaultShippingMethod'] = $defaultShippingMethod;
        }
        if ($this->config->getDefaultPaymentMethod()) {
            $config['defaultPaymentMethod'] = $this->config->getDefaultPaymentMethod();
        }
        if ($quote->getIsVirtual()) {
            unset($config['paymentMethods']);
        }
        return $config;
    }

    /**
     * Retrieve default redirect on empty quote page URL
     *
     * @return string
     */
    private function getDefaultRedirectOnEmptyQuoteUrl()
    {
        $url = $this->checkoutSession->getContinueShoppingUrl(true);
        if (!$url) {
            $url = $this->urlBuilder->getUrl();
        }
        return $url;
    }
}
