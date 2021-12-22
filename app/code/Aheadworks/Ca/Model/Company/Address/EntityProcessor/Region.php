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
namespace Aheadworks\Ca\Model\Company\Address\EntityProcessor;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Directory\Model\RegionFactory;

/**
 * Class Region
 * @package Aheadworks\Ca\Model\Company\Address\EntityProcessor
 */
class Region
{
    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        RegionFactory $regionFactory
    ) {
        $this->regionFactory = $regionFactory;
    }

    /**
     * Prepare region data
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $regionData = [
            RegionInterface::REGION_ID => $data[AddressInterface::REGION_ID]
        ];

        if ($data[AddressInterface::REGION_ID]) {
            $region = $this->regionFactory->create()->load($data[AddressInterface::REGION_ID]);
            $regionData[RegionInterface::REGION] = $region->getName();
        } else {
            $regionData[RegionInterface::REGION] = $data[AddressInterface::REGION];
        }
        $data[AddressInterface::REGION] = $regionData;

        return $data;
    }
}
