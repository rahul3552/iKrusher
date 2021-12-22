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
namespace Aheadworks\OneStepCheckout\Test\Unit\Model;

use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\Config
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->config = $objectManager->getObject(
            Config::class,
            ['scopeConfig' => $this->scopeConfigMock]
        );
    }

    public function testGetCheckoutTitle()
    {
        $title = 'One Step Checkout';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_CHECKOUT_TITLE,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($title);
        $this->assertEquals($title, $this->config->getCheckoutTitle());
    }

    public function testGetCheckoutDescription()
    {
        $description = 'Checkout description';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_CHECKOUT_DESCRIPTION,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($description);
        $this->assertEquals($description, $this->config->getCheckoutDescription());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsApplyDiscountCodeEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_APPLY_DISCOUNT_CODE_ENABLE,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($value);
        $this->assertSame($value, $this->config->isApplyDiscountCodeEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsOrderNoteEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_APPLY_ORDER_NOTE_ENABLE,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($value);
        $this->assertSame($value, $this->config->isOrderNoteEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsMiniCartExpanded($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_MINI_CART_EXPANDED,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($value);
        $this->assertSame($value, $this->config->isMiniCartExpanded());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsDisplayTopMenu($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_DISPLAY_TOP_MENU,
                ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($value);
        $this->assertSame($value, $this->config->isDisplayTopMenu());
    }

    /**
     * Test for isEnabledCheckoutStatistics method
     */
    public function testIsEnabledCheckoutStatistics()
    {
        $value = 1;
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_ENABLE_CHECKOUT_STATISTICS)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isEnabledCheckoutStatistics());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsNewsletterSubscribeOptionEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_NEWSLETTER_SUBSCRIBE_ENABLE,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($value);
        $this->assertSame($value, $this->config->isNewsletterSubscribeOptionEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsNewsletterSubscribeOptionCheckedByDefault($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_NEWSLETTER_SUBSCRIBE_CHECKED,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($value);
        $this->assertSame($value, $this->config->isNewsletterSubscribeOptionCheckedByDefault());
    }

    public function testGetDefaultCountryId()
    {
        $countryId = 'US';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_DEFAULT_COUNTRY_ID,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($countryId);
        $this->assertEquals($countryId, $this->config->getDefaultCountryId());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsBillingShippingAreTheSame($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_BILLING_SHIPPING_ARE_THE_SAME,
                ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($value);
        $this->assertSame($value, $this->config->isBillingShippingAreTheSame());
    }

    /**
     * @return array
     */
    public function boolDataProvider()
    {
        return [[true], [false]];
    }
}
