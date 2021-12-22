<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Block\System\Config;

/**
 * Class to make field are readOnly of i95dev_adapter_configurations
 */

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class for Disabling button
 */
class Disable extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Set field as readonly
     * @param AbstractElement $element
     * @return type boolian
     * @author Chandra Prasad
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $element->setData('readonly', 1);

        return $element->getElementHtml();
    }
}
