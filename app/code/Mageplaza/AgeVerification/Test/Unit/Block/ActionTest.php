<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Test\Unit\Block;

use Magento\Cms\Model\Page as CmsPage;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Mageplaza\AgeVerification\Block\Action;
use Mageplaza\AgeVerification\Helper\Data as HelperData;
use Mageplaza\AgeVerification\Helper\Image as HelperImage;
use Mageplaza\AgeVerification\Model\ConditionFactory;
use Mageplaza\AgeVerification\Model\PurchaseCondition;
use Mageplaza\AgeVerification\Model\PurchaseConditionFactory as PurchaseFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class ActionTest
 * @package Mageplaza\AgeVerification\Test\Unit\Block
 */
class ActionTest extends TestCase
{
    /**
     * @var Template\Context|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var HelperData|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperData;

    /**
     * @var CmsPage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmsPage;

    /**
     * @var Registry|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registry;

    /**
     * @var HelperImage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperImage;

    /**
     * @var SessionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionFactory;

    /**
     * @var Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerSession;

    /**
     * @var HttpContext|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_context;

    /**
     * @var Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

    /**
     * @var ConditionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_conditionFactory;

    /**
     * @var PurchaseFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_purchaseFactory;

    /**
     * @var Action|PHPUnit_Framework_MockObject_MockObject
     */
    private $object;

    /**
     * @var RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $_request;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder(Template\Context::class)->disableOriginalConstructor()->getMock();
        $this->_helperData = $this->getMockBuilder(HelperData::class)->disableOriginalConstructor()->getMock();
        $this->_cmsPage = $this->getMockBuilder(CmsPage::class)->disableOriginalConstructor()->getMock();
        $this->_registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
        $this->_helperImage = $this->getMockBuilder(HelperImage::class)->disableOriginalConstructor()->getMock();
        $this->_sessionFactory = $this->getMockBuilder(SessionFactory::class)->setMethods(['create'])->disableOriginalConstructor()->getMock();
        $this->_customerSession = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
        $this->_context = $this->getMockBuilder(HttpContext::class)->disableOriginalConstructor()->getMock();
        $this->_response = $this->getMockBuilder(Http::class)->disableOriginalConstructor()->getMock();
        $this->_conditionFactory = $this->getMockBuilder(ConditionFactory::class)->setMethods(['create'])->disableOriginalConstructor()->getMock();
        $this->_purchaseFactory = $this->getMockBuilder(PurchaseFactory::class)->setMethods(['create'])->disableOriginalConstructor()->getMock();
        $this->_request = $this->getMockBuilder(RequestInterface::class)->setMethods([
            'getFullActionName',
            'getRequestUri'
        ])->getMockForAbstractClass();
        $this->context->expects($this->any())->method('getRequest')->willReturn($this->_request);

        $this->object = new Action(
            $this->context,
            $this->_helperData,
            $this->_cmsPage,
            $this->_registry,
            $this->_helperImage,
            $this->_sessionFactory,
            $this->_customerSession,
            $this->_context,
            $this->_response,
            $this->_conditionFactory,
            $this->_purchaseFactory,
            []
        );
    }

    public function testAdminInstance()
    {
        $this->assertInstanceOf(Action::class, $this->object);
    }

    public function testCheckCustomerGroups()
    {
        $customerGroups = '0,1,2';
        $this->_helperData->method('getCustomerGroupsConfig')->willReturn($customerGroups);

        $customer = $this->getMockBuilder(Customer::class)->setMethods(['getGroupId'])->disableOriginalConstructor()->getMock();
        $this->_customerSession->method('getCustomer')->willReturn($customer);

        $groupId = 1;
        $customer->method('getGroupId')->willReturn($groupId);

        $this->assertEquals(true, $this->object->checkCustomerGroups());
    }

    public function testCheckPurchaseVerify()
    {
        $enable = true;
        $this->_helperData->method('isEnablePurchase')->willReturn($enable);

        $customerGroups = '0,1,2';
        $this->_helperData->method('getCustomerGroupsConfig')->willReturn($customerGroups);

        $customer = $this->getMockBuilder(Customer::class)->setMethods(['getGroupId'])->disableOriginalConstructor()->getMock();
        $this->_customerSession->method('getCustomer')->willReturn($customer);

        $groupId = 1;
        $customer->method('getGroupId')->willReturn($groupId);

        $condition = '';
        $this->_helperData->method('getPurchaseCondition')->willReturn($condition);

        $fullActionName = 'catalog_product_view';
        $this->_request->method('getFullActionName')->willReturn($fullActionName);

        $registry = $this->getMockBuilder(Registry::class)->setMethods([
            'registry',
            'getId'
        ])->disableOriginalConstructor()->getMock();
        $this->_registry->method('registry')->with('current_product')->willReturn($registry);

        $proId = 1;
        $registry->method('getId')->willReturn($proId);

        $condition = $this->getMockBuilder(PurchaseCondition::class)->setMethods(['getMatchingProductIds'])->disableOriginalConstructor()->getMock();
        $this->_purchaseFactory->method('create')->willReturn($condition);

        $proIds = [1, 2, 3];
        $condition->method('getMatchingProductIds')->with('')->willReturn($proIds);

        $this->assertEquals(true, $this->object->checkPurchaseVerify());
    }

    public function testCheckCategoryPagesVerify()
    {
        $excludePages = '';
        $this->_helperData->method('getExcludePages')->willReturn($excludePages);

        $catIds = '0,2,11';
        $this->_helperData->method('getCategoryConfig')->willReturn($catIds);

        $registry = $this->getMockBuilder(Registry::class)->setMethods([
            'registry',
            'getId'
        ])->disableOriginalConstructor()->getMock();
        $this->_registry->method('registry')->with('current_category')->willReturn($registry);

        $catId = 2;
        $registry->method('getId')->willReturn($catId);

        $this->assertEquals(true, $this->object->checkCategoryPages());
    }

    public function testAutoVerify()
    {
        $isCustomerLoggedIn = true;
        $this->_context->method('getValue')->with('customer_logged_in')->willReturn($isCustomerLoggedIn);

        $isAutoVerify = true;
        $this->_helperData->method('isAutoVerify')->willReturn($isAutoVerify);

        $customer = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
        $this->_sessionFactory->method('create')->willReturn($customer);

        $customerData = $this->getMockBuilder(CustomerInterface::class)->getMock();
        $customer->method('getCustomerData')->willReturn($customerData);

        $dob = '1994/9/12';
        $customerData->method('getDob')->willReturn($dob);

        $age = 18;
        $this->_helperData->method('getAgeVerify')->willReturn($age);

        $this->assertEquals(true, $this->object->autoVerify());
    }
}
