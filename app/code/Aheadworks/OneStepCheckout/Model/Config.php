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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Config
 * @package Aheadworks\OneStepCheckout\Model
 */
class Config
{
    /**
     * Configuration path to checkout title
     */
    const XML_PATH_CHECKOUT_TITLE = 'aw_osc/general/title';

    /**
     * Configuration path to checkout description
     */
    const XML_PATH_CHECKOUT_DESCRIPTION = 'aw_osc/general/description';

    /**
     * Configuration path to apply discount code enable flag
     */
    const XML_PATH_APPLY_DISCOUNT_CODE_ENABLE = 'aw_osc/general/apply_discount_code';

    /**
     * Configuration path to order note enable flag
     */
    const XML_PATH_APPLY_ORDER_NOTE_ENABLE = 'aw_osc/general/order_note_enabled';

    /**
     * Configuration path to Google Places API key
     */
    const XML_PATH_GOOGLE_AUTO_COMPLETE_ENABLED = 'aw_osc/general/google_autocomplete_enabled';

    /**
     * Configuration path to Google autocomplete enabled flag
     */
    const XML_PATH_GOOGLE_PLACES_API_KEY = 'aw_osc/general/google_places_api_key';

    /**
     * Configuration path to expand mini-cart by default flag
     */
    const XML_PATH_MINI_CART_EXPANDED = 'aw_osc/general/mini_cart_expanded';

    /**
     * Configuration path to expand mini-cart by default flag
     */
    const XML_PATH_DISPLAY_TOP_MENU = 'aw_osc/general/display_top_menu';

    /**
     * Configuration path to enable checkout statistics by default flag
     */
    const XML_PATH_ENABLE_CHECKOUT_STATISTICS = 'aw_osc/general/enable_checkout_statistics';

    /**
     * Configuration path to newsletter subscribe option enable flag
     */
    const XML_PATH_NEWSLETTER_SUBSCRIBE_ENABLE = 'aw_osc/newsletter/enable';

    /**
     * Configuration path to newsletter subscribe checked by default
     */
    const XML_PATH_NEWSLETTER_SUBSCRIBE_CHECKED = 'aw_osc/newsletter/checked_by_default';

    /**
     * Configuration path to default value of country
     */
    const XML_PATH_DEFAULT_COUNTRY_ID = 'aw_osc/default_values/country_id';

    /**
     * Configuration path to default value of region
     */
    const XML_PATH_DEFAULT_REGION_ID = 'aw_osc/default_values/region_id';

    /**
     * Configuration path to default value of city
     */
    const XML_PATH_DEFAULT_CITY = 'aw_osc/default_values/city';

    /**
     * Configuration path to default shipping method
     */
    const XML_PATH_DEFAULT_SHIPPING_METHOD = 'aw_osc/default_values/shipping_method';

    /**
     * Configuration path to default payment method
     */
    const XML_PATH_DEFAULT_PAYMENT_METHOD = 'aw_osc/default_values/payment_method';

    /**
     * Configuration path to delivery date display option
     */
    const XML_PATH_DELIVERY_DATE_DISPLAY_OPTION = 'aw_osc/delivery_date/display_option';

    /**
     * Configuration path to delivery date available weekdays
     */
    const XML_PATH_DELIVERY_DATE_AVAILABLE_WEEKDAYS = 'aw_osc/delivery_date/available_weekdays';

    /**
     * Configuration path to delivery date available time slots
     */
    const XML_PATH_DELIVERY_DATE_AVAILABLE_TIME_SLOTS = 'aw_osc/delivery_date/available_time_slots';

    /**
     * Configuration path to non delivery periods
     */
    const XML_PATH_DELIVERY_DATE_NON_DELIVERY_PERIODS = 'aw_osc/delivery_date/non_delivery_periods';

    /**
     * Configuration path to minimal order delivery period
     */
    const XML_PATH_DELIVERY_DATE_MIN_ORDER_DELIVERY_PERIOD = 'aw_osc/delivery_date/min_order_delivery_period';

    /**
     * Configuration path to billing fields customization config
     */
    const XML_PATH_BILLING_ADDRESS_FORM_CUSTOMIZATION = 'aw_osc/billing_customization/fields_customization';

    /**
     * Configuration path to billing and shipping addresses are the same by option
     */
    const XML_PATH_BILLING_SHIPPING_ARE_THE_SAME = 'aw_osc/addresses_settings/billing_shipping_are_the_same';

    /**
     * Configuration path to shipping fields customization config
     */
    const XML_PATH_SHIPPING_ADDRESS_FORM_CUSTOMIZATION = 'aw_osc/shipping_customization/fields_customization';

    /**
     * Configuration path to trust seals block enabled flag
     */
    const XML_PATH_TRUST_SEALS_ENABLED = 'aw_osc/trust_seals/enabled';

    /**
     * Configuration path to trust seals block label
     */
    const XML_PATH_TRUST_SEALS_LABEL = 'aw_osc/trust_seals/label';

    /**
     * Configuration path to trust seals block text
     */
    const XML_PATH_TRUST_SEALS_TEXT = 'aw_osc/trust_seals/text';

    /**
     * Configuration path to trust seals block badges
     */
    const XML_PATH_TRUST_SEALS_BADGES = 'aw_osc/trust_seals/badges';

    /**
     * Configuration path to geo ip detection enabled flag
     */
    const XML_PATH_GEO_IP_ENABLED = 'aw_osc/geo_ip/enabled';

    /**
     * Configuration path to geo ip detection license key
     */
    const XML_PATH_GEO_IP_LICENSE_KEY = 'aw_osc/geo_ip/license_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * Get checkout page title
     *
     * @return string
     */
    public function getCheckoutTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHECKOUT_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get checkout page description
     *
     * @return string
     */
    public function getCheckoutDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHECKOUT_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if apply discount code enabled
     *
     * @return bool
     */
    public function isApplyDiscountCodeEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_APPLY_DISCOUNT_CODE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if order note enabled
     *
     * @return bool
     */
    public function isOrderNoteEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_APPLY_ORDER_NOTE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if mini cart is expanded by default
     *
     * @return bool
     */
    public function isMiniCartExpanded()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_MINI_CART_EXPANDED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if display top menu
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isDisplayTopMenu($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_TOP_MENU,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if check out reporting is enabled
     *
     * @return bool
     */
    public function isEnabledCheckoutStatistics()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_CHECKOUT_STATISTICS);
    }

    /**
     * Check if newsletter subscribe option enabled
     *
     * @return bool
     */
    public function isNewsletterSubscribeOptionEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NEWSLETTER_SUBSCRIBE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if newsletter subscribe option checked by default
     *
     * @return bool
     */
    public function isNewsletterSubscribeOptionCheckedByDefault()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NEWSLETTER_SUBSCRIBE_CHECKED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default country ID
     *
     * @return string|null
     */
    public function getDefaultCountryId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_COUNTRY_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default region ID
     *
     * @return int|string|null
     */
    public function getDefaultRegionId()
    {
        if (!$this->getDefaultCountryId()) {
            return null;
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_REGION_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default city
     *
     * @return string|null
     */
    public function getDefaultCity()
    {
        if (!$this->getDefaultCountryId()) {
            return null;
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_CITY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default shipping method
     *
     * @return string
     */
    public function getDefaultShippingMethod()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_SHIPPING_METHOD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default payment method
     *
     * @return string
     */
    public function getDefaultPaymentMethod()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_PAYMENT_METHOD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get delivery date display option
     *
     * @return int
     */
    public function getDeliveryDateDisplayOption()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_DISPLAY_OPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get weekdays available for delivery
     *
     * @return array
     */
    public function getDeliveryDateAvailableWeekdays()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_AVAILABLE_WEEKDAYS,
            ScopeInterface::SCOPE_STORE
        );
        return (empty($value)) ? [] : explode(',', $value);
    }

    /**
     * Get time slots available for delivery
     *
     * @return array
     */
    public function getDeliveryDateTimeSlots()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_AVAILABLE_TIME_SLOTS,
            ScopeInterface::SCOPE_STORE
        );
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Get non delivery periods
     *
     * @return array
     */
    public function getNonDeliveryPeriods()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_NON_DELIVERY_PERIODS,
            ScopeInterface::SCOPE_STORE
        );
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Get minimal order delivery period
     *
     * @return int
     */
    public function getMinOrderDeliveryPeriod()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_MIN_ORDER_DELIVERY_PERIOD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get address form config
     *
     * @param string $addressType
     * @return array
     */
    public function getAddressFormConfig($addressType)
    {
        $path = $addressType == 'billing'
            ? self::XML_PATH_BILLING_ADDRESS_FORM_CUSTOMIZATION
            : self::XML_PATH_SHIPPING_ADDRESS_FORM_CUSTOMIZATION;
        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Check if billing and shipping are the same
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isBillingShippingAreTheSame($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BILLING_SHIPPING_ARE_THE_SAME,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if trust seals block enabled
     *
     * @return bool
     */
    public function isTrustSealsBlockEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TRUST_SEALS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get trust seals block label
     *
     * @return string
     */
    public function getTrustSealsLabel()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TRUST_SEALS_LABEL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get trust seals block text
     *
     * @return string
     */
    public function getTrustSealsText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TRUST_SEALS_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get trust seals badges
     *
     * @return array
     */
    public function getTrustSealsBadges()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TRUST_SEALS_BADGES,
            ScopeInterface::SCOPE_STORE
        );
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Check if geo ip detection enabled
     *
     * @return bool
     */
    public function isGeoIpDetectionEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GEO_IP_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get geoIp license key
     *
     * @return bool
     */
    public function getLicenseKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GEO_IP_LICENSE_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if Google autocomplete enabled
     *
     * @return bool
     */
    public function isGoogleAutoCompleteEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GOOGLE_AUTO_COMPLETE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get Google Places API key
     *
     * @return string
     */
    public function getGooglePlacesApiKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_PLACES_API_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
