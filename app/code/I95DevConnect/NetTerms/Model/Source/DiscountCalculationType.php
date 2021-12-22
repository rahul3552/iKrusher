<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class for Discount calculation type
 */
class DiscountCalculationType implements ArrayInterface
{

    const DISCOUNTCALTYPE_PERCENTAGE = 1;
    const DISCOUNTCALTYPE_AMOUNT = 2;

    /*
     * Return array of options
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DISCOUNTCALTYPE_PERCENTAGE, 'label' => __('Percentage')],
            ['value' => self::DISCOUNTCALTYPE_AMOUNT, 'label' => __('Amount')]
        ];
    }
}
