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
 * @package     Mageplaza_ShippingRestriction
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShippingRestriction\Model\Condition;

use Magento\Framework\Data\Form;
use Magento\Rule\Model\Condition\AbstractCondition as MagentoAbstractCondition;

/**
 * Class AbstractCondition
 * @method setFormName()
 * @package Mageplaza\ShippingRestriction\Model\Condition
 */
class AbstractCondition extends MagentoAbstractCondition
{
    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if ($this->_defaultOperatorInputByType === null) {
            $this->_defaultOperatorInputByType = parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['string'] =
                ['==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()', '*=', '=*'];
        }

        return $this->_defaultOperatorInputByType;
    }

    /**
     * Default operator options getter.
     *
     * Provides all possible operator options.
     *
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        $this->_defaultOperatorOptions = array_merge(parent::getDefaultOperatorOptions(), [
            '=*' => __('start from'),
            '*=' => __('end with'),
        ]);

        return $this->_defaultOperatorOptions;
    }

    /**
     * Get rule form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->getRule()->getForm();
    }
}
