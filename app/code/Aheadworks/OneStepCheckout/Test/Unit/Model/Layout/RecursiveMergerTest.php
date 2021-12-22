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
namespace Aheadworks\OneStepCheckout\Test\Unit\Model\Layout;

use Aheadworks\OneStepCheckout\Model\Layout\RecursiveMerger;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\Layout\RecursiveMergerTest
 */
class RecursiveMergerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RecursiveMerger
     */
    private $merger;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->merger = $objectManager->getObject(RecursiveMerger::class);
    }

    /**
     * @param array $customConfig
     * @param array $originalConfig
     * @param array $result
     * @dataProvider mergeDataProvider
     */
    public function testMerge($customConfig, $originalConfig, $result)
    {
        $this->assertEquals($result, $this->merger->merge($customConfig, $originalConfig));
    }

    /**
     * @return array
     */
    public function mergeDataProvider()
    {
        return [
            [
                ['key' => 'value1'],
                ['key' => 'value2'],
                ['key' => 'value2']
            ],
            [
                ['key1' => 'value1'],
                ['key2' => 'value2'],
                [
                    'key1' => 'value1',
                    'key2' => 'value2'
                ]
            ],
            [
                [],
                ['key' => 'value'],
                ['key' => 'value']
            ],
            [
                [
                    'keyLevel0' => [
                        'key1Level1' => 'value1'
                    ]
                ],
                [
                    'keyLevel0' => [
                        'key1Level1' => 'value2'
                    ]
                ],
                [
                    'keyLevel0' => [
                        'key1Level1' => 'value2'
                    ]
                ]
            ],
            [
                [
                    'keyLevel0' => [
                        'key1Level1' => 'value1'
                    ]
                ],
                [
                    'keyLevel0' => [
                        'key1Level2' => 'value2'
                    ]
                ],
                [
                    'keyLevel0' => [
                        'key1Level1' => 'value1',
                        'key1Level2' => 'value2'
                    ]
                ]
            ]
        ];
    }
}
