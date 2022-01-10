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
 * Class Icon
 * @package Mageplaza\ProductAttachments\Model\Config\Source
 */
class Icon implements ArrayInterface
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var mixed
     */
    protected $_iconList;

    /**
     * Icon constructor.
     *
     * @param Data $helperData
     *
     */
    public function __construct(Data $helperData)
    {
        $this->_helperData = $helperData;
        $this->_iconList = $this->_helperData->unserialize($this->_helperData->getConfigGeneral('manage_icon/icons'));
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = ['' => __('-- Please select --')];

        foreach ($this->_iconList as $value => $label) {
            $options[] = [
                'value' => $label['file_icon'],
                'label' => $label['file_type']
            ];
        }

        return $options;
    }
}
