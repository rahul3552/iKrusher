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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Email;

/**
 * Class VariableProcessorComposite
 * @package Aheadworks\Ca\Model\Email
 */
class VariableProcessorComposite implements VariableProcessorInterface
{
    /**
     * @var VariableProcessorInterface[]
     */
    private $processors;

    /**
     * @param VariableProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare variables
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables)
    {
        foreach ($this->processors as $processor) {
            $variables = $processor->prepareVariables($variables);
        }
        return $variables;
    }
}
