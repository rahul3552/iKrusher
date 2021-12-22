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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Converter;

use Magento\Framework\DataObject;

/**
 * Class DataObjectConverter
 * @package Aheadworks\Ctq\Model\Converter
 */
class DataObjectConverter
{
    /**
     * Convert data object to flat array
     *
     * @param DataObject $object
     * @return array
     */
    public function convertObjectToFlatArray($object)
    {
        $outputData = $object->getData();

        $outputData = $this->excludeComplexValue($outputData);

        return $outputData;
    }

    /**
     * Exclude complex value from array
     *
     * @param array $array
     * @return array
     */
    protected function excludeComplexValue(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_object($value) || is_array($value)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
