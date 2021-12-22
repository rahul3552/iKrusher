<?php
/**
 * Copyright � Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author Arushi Bansal
 */
namespace I95DevConnect\MessageQueue\Plugin;

/**
 * Class RulePlugin for disabling default
 */
class RulePlugin
{
    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * Rule plugin constructor.
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Get catalog rules product price for specific date, website and
     * customer group
     *
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule $subject
     * @param $result
     *
     * @return float|false
     */
    public function afterGetRulePrice(\Magento\CatalogRule\Model\ResourceModel\Rule $subject, $result) //NOSONAR
    {
        if ($this->dataHelper->getGlobalValue('i95_skip_final_price')) {
            return false;
        } else {

            return $result;
        }
    }
}
