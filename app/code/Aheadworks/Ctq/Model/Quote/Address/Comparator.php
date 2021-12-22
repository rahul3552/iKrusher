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
namespace Aheadworks\Ctq\Model\Quote\Address;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Comparator
 *
 * @package Aheadworks\Ctq\Model\Quote\Address
 */
class Comparator
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @param Converter $converter
     */
    public function __construct(
        Converter $converter
    ) {
        $this->converter = $converter;
    }

    /**
     * Check if two addresses are equal
     *
     * @param AddressInterface $address1
     * @param AddressInterface $address2
     * @param bool $isCountConsidered
     * @return bool
     * @throws LocalizedException
     */
    public function isEqual(AddressInterface $address1, AddressInterface $address2, $isCountConsidered = true)
    {
        $address1AsArray = $this->converter->convertToArray($address1);
        $address2AsArray = $this->converter->convertToArray($address2);

        $diff = $this->findDiffInArrays($address1AsArray, $address2AsArray);

        $isCountEqual = true;
        if ($isCountConsidered) {
            $isCountEqual = (count($address1AsArray) == count($address2AsArray));
        }

        return empty($diff) && $isCountEqual;
    }

    /**
     * Get array with data differences
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    private function findDiffInArrays($array1, $array2)
    {
        $result = [];
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                if ($value != $array2[$key]) {
                    $result[$key] = true;
                }
            } else {
                $result[$key] = true;
            }
        }
        return $result;
    }
}
