<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Block\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class for mainatining the status of ShippingMapping enabled field
 */
class ShippingMapStatus extends \Magento\Framework\App\Config\Value
{

    /**
     * @return ShippingMapStatus|void
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Cannot change value of Shipping mapping status')
            );
        }
        parent::beforeSave();
    }
}
