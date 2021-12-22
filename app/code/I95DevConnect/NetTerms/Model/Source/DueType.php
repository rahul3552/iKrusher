<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class DueType implements ArrayInterface
{

    const DUETYPE_NETDAYS = 1;
    const DUETYPE_DATE = 2;
    const DUETYPE_EOM = 3;
    const DUETYPE_NONE = 4;
    const DUETYPE_NEXTMONTH = 5;
    const VALUE = 'value';
    const LABEL = 'label';

    /**
     * Return array of options
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [self::VALUE => self::DUETYPE_NETDAYS, self::LABEL => __('Net Days')],
            [self::VALUE => self::DUETYPE_DATE, self::LABEL => __('Date')],
            [self::VALUE => self::DUETYPE_EOM, self::LABEL => __('EOM')],
            [self::VALUE => self::DUETYPE_NONE, self::LABEL => __('NONE')],
            [self::VALUE => self::DUETYPE_NEXTMONTH, self::LABEL => __('Next Month')]
        ];
    }
}
