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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Test\Unit\Model\Service;

use Aheadworks\CreditLimit\Model\Service\CustomerGroupService;
use Aheadworks\CreditLimit\Model\ResourceModel\CustomerGroupConfig;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for CustomerGroupService
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Service
 */
class CustomerGroupServiceTest extends TestCase
{
    /**
     * @var CustomerGroupService
     */
    private $model;

    /**
     * @var CustomerGroupConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerGroupConfigResourceMock;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->customerGroupConfigResourceMock = $this->getMockBuilder(CustomerGroupConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadConfigValue', 'loadData'])
            ->getMock();

        $this->model = $objectManager->getObject(
            CustomerGroupService::class,
            [
                'customerGroupConfigResource' => $this->customerGroupConfigResourceMock
            ]
        );
    }

    /**
     * Test for getCreditLimit method
     */
    public function testGetCreditLimit()
    {
        $groupId = 3;
        $websiteId = 1;
        $creditLimitValue = 20;

        $customerGroupConfig = [
            'customer_group_id' => $groupId,
            SummaryInterface::CREDIT_LIMIT => $creditLimitValue
        ];

        $this->customerGroupConfigResourceMock->expects($this->once())
            ->method('loadConfigValue')
            ->with($websiteId)
            ->willReturn([$customerGroupConfig]);

        $this->assertSame($creditLimitValue, $this->model->getCreditLimit($groupId, $websiteId));
    }

    /**
     * Test for getCreditLimitValuesForWebsite method
     */
    public function testGetCreditLimitValuesForWebsite()
    {
        $groupId = 3;
        $websiteId = 1;
        $creditLimitValue = 20;

        $customerGroupConfig = [
            'customer_group_id' => $groupId,
            SummaryInterface::CREDIT_LIMIT => $creditLimitValue
        ];

        $result = [
            $groupId => $customerGroupConfig[SummaryInterface::CREDIT_LIMIT]
        ];

        $this->customerGroupConfigResourceMock->expects($this->once())
            ->method('loadData')
            ->with($websiteId)
            ->willReturn([$customerGroupConfig]);

        $this->assertSame($result, $this->model->getCreditLimitValuesForWebsite($websiteId));
    }
}
