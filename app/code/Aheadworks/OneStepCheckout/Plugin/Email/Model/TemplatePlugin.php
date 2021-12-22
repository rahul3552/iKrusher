<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Plugin\Email\Model;

use Aheadworks\OneStepCheckout\Model\Order\Email\Source\Variable as VariableSource;
use Magento\Email\Model\Template;

/**
 * Class TemplatePlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Email\Model
 */
class TemplatePlugin
{
    /**
     * @var VariableSource
     */
    private $variableSource;

    /**
     * @param VariableSource $variableSource
     */
    public function __construct(
        VariableSource $variableSource
    ) {
        $this->variableSource = $variableSource;
    }

    /**
     * Add additional variable options for order templates
     *
     * @param Template $subject
     * @param array $result
     * @return array
     */
    public function afterGetVariablesOptionArray($subject, $result)
    {
        if ($this->variableSource->isSalesOrderTemplate($subject->getOrigTemplateCode())) {
            $oscVariables = $this->variableSource->toOptionArray();
            $result['value'] = array_merge($result['value'], $oscVariables);
        }

        return $result;
    }
}
