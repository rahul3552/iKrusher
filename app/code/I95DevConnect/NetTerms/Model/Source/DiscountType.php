<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class for discount type
 */
class DiscountType implements ArrayInterface
{

    const DISCOUNTTYPE_DAYS = 1;
    const DISCOUNTTYPE_DATE = 2;
    const DISCOUNTTYPE_EOM = 3;
    const DISCOUNTTYPE_NONE = 4;
    const VALUE = 'value';
    const LABEL = 'label';

    /**
     * Return array of options
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [self::VALUE => self::DISCOUNTTYPE_DAYS, self::LABEL => __('Net Days')],
            [self::VALUE => self::DISCOUNTTYPE_DATE, self::LABEL => __('Date')],
            [self::VALUE => self::DISCOUNTTYPE_EOM, self::LABEL => __('EOM')],
            [self::VALUE => self::DISCOUNTTYPE_NONE, self::LABEL => __('NONE')]
        ];
    }
}
