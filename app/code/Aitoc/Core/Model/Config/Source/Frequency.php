<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_Core
 */


namespace Aitoc\Core\Model\Config\Source;

class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => 1,
                'label' => __('1 Day')
            ],
            [
                'value' => 5,
                'label' => __('5 Days')
            ],
            [
                'value' => 10,
                'label' => __('10 Days')
            ]
        ];

        return $options;
    }
}
