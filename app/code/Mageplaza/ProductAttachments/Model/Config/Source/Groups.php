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
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\ProductAttachments\Helper\Data;

/**
 * Class Groups
 * @package Mageplaza\ProductAttachments\Model\Config\Source
 */
class Groups implements ArrayInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Groups constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = [
            'value' => '',
            'label' => __('-- Please Select --')
        ];

        $groups = $this->helperData->getGroups();
        $groups = $groups ? Data::jsonDecode($groups) : [];

        foreach ($groups as $group) {
            if (isset($group['value'])) {
                usort($group['value'], $this->helperData->sortByPosition());
                foreach ($group['value'] as $value) {
                    $options[] = [
                        'value' => $value['value'],
                        'label' => $value['name']
                    ];
                }
            }
        }

        return $options;
    }
}
