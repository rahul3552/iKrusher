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
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Model\Condition;

use Magento\CatalogRule\Model\Rule\Condition\Combine as ConditionCombine;
use Magento\CatalogRule\Model\Rule\Condition\ProductFactory;
use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 * @package Mageplaza\AgeVerification\Model\Condition
 */
class Combine extends ConditionCombine
{
    /**
     * Combine constructor.
     *
     * @param Context $context
     * @param ProductFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductFactory $conditionFactory,
        array $data = []
    ) {
        parent::__construct($context, $conditionFactory, $data);

        $this->setType(__CLASS__);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return 'purchase_conditions';
    }

    /**
     * @inheritdoc
     */
    public function loadArray($arr, $key = 'conditions')
    {
        return parent::loadArray($arr, 'purchase_conditions');
    }

    /**
     * @inheritdoc
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();

        foreach ($conditions as &$condition) {
            if ($condition['value'] === ConditionCombine::class) {
                $condition['value'] = __CLASS__;
                break;
            }
        }

        return $conditions;
    }
}
