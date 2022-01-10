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
 * @category  Mageplaza
 * @package   Mageplaza_BetterWishlist
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Test\Unit\Block;

use Magento\Cms\Helper\Page as CmsPage;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\CustomForm\Block\CustomForm;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Form as CustomFormModel;
use Mageplaza\CustomForm\Model\FormFactory as CustomFormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as CustomFormResource;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class CustomFormTest
 * @package Mageplaza\CustomForm\Test\Unit\Block
 */
class CustomFormTest extends TestCase
{
    private $object;

    /**
     * @var Template\Context|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var StoreManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var DateTime|PHPUnit_Framework_MockObject_MockObject
     */
    protected $date;

    /**
     * @var CmsPage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $cmsPage;

    /**
     * @var CustomFormFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $customFormFactory;

    /**
     * @var CustomFormResource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $customFormResource;

    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperData;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $this->request = $this->getMockBuilder(RequestInterface::class)->setMethods(
            []
        )->disableOriginalConstructor()->getMock();
        $this->date = $this->getMockBuilder(DateTime::class)
            ->setMethods([])
            ->disableOriginalConstructor()->getMock();
        $this->cmsPage = $this->getMockBuilder(CmsPage::class)
            ->setMethods([])
            ->disableOriginalConstructor()->getMock();
        $this->customFormFactory = $this->getMockBuilder(CustomFormFactory::class)
            ->setMethods([])
            ->disableOriginalConstructor()->getMock();
        $this->customFormResource = $this->getMockBuilder(CustomFormResource::class)
            ->setMethods([])
            ->disableOriginalConstructor()->getMock();
        $this->helperData = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->_storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->getMock();
        $this->context->method('getStoreManager')->willReturn($this->_storeManager);
        $this->object = new CustomForm(
            $this->context,
            $this->request,
            $this->date,
            $this->cmsPage,
            $this->customFormFactory,
            $this->customFormResource,
            $this->helperData
        );
    }

    public function testAdminInstance()
    {
        $this->assertInstanceOf(CustomForm::class, $this->object);
    }

    public function testLoadCustomForm()
    {
        $customForm = $this->getMockBuilder(CustomFormModel::class)->setMethods([])
            ->disableOriginalConstructor()->getMock();
        $this->customFormFactory->method('create')->willReturn($customForm);
        $formId = null;
        $this->customFormResource->method('load')->with($customForm, $formId)->willReturn($customForm);

        $this->assertEquals($customForm, $this->object->loadCustomForm());
    }

    public function testIsValidForm()
    {
        $customForm = $this->getMockBuilder(CustomFormModel::class)->setMethods([
            'getStatus',
            'getValidFromDate',
            'getValidToDate',
            'getStoreIds',
        ])
            ->disableOriginalConstructor()->getMock();
        $this->customFormFactory->method('create')->willReturn($customForm);
        $formId = null;
        $this->customFormResource->method('load')->with($customForm, $formId)->willReturn($customForm);
        $customForm->method('getStatus')->willReturn('1');
        $customForm->method('getValidFromDate')->willReturn('2019-4-1');
        $customForm->method('getValidToDate')->willReturn('2019-6-1');
        $customForm->method('getStoreIds')->willReturn(['0']);

        $this->date->method('date')->with('Y-m-d')->willReturn('2019-5-1');
        $store = $this->getMockBuilder(StoreInterface::class)->setMethods([])
            ->disableOriginalConstructor()->getMock();
        $this->_storeManager->method('getStore')->willReturn($store);
        $storeId = '1';
        $store->method('getId')->willReturn($storeId);

        $this->assertEquals(true, $this->object->isValidForm());
    }

    public function testGetCustomFormData()
    {
        $customForm = $this->getMockBuilder(CustomFormModel::class)->setMethods([
            'getCustomForm'
        ])->disableOriginalConstructor()->getMock();
        $this->customFormFactory->method('create')->willReturn($customForm);
        $formId = null;
        $this->customFormResource->method('load')->with($customForm, $formId)->willReturn($customForm);

        $customForm->method('getCustomForm')->willReturn('{}');

        $this->helperData->method('jsDecode')->with('{}')->willReturn([]);

        $this->assertEquals([], $this->object->getCustomFormData(true));
    }

    public function testGetRedirectUrl()
    {
        $customForm = $this->getMockBuilder(CustomFormModel::class)->setMethods([
            'getActionAfterSubmit',
            'getPageUrl',
            'getCmsPage',
        ])->disableOriginalConstructor()->getMock();
        $this->customFormFactory->method('create')->willReturn($customForm);
        $formId = null;
        $this->customFormResource->method('load')->with($customForm, $formId)->willReturn($customForm);
        $customForm->method('getActionAfterSubmit')->willReturn('cms');
        $customForm->method('getPageUrl')->willReturn('pageUrl');
        $customForm->method('getCmsPage')->willReturn('home');

        $this->cmsPage->method('getPageUrl')->with('home')->willReturn('localhost.com/home');

        $this->assertEquals('localhost.com/home', $this->object->getRedirectUrl());
    }
}
