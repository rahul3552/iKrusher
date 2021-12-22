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
 * Interface VariableProcessorInterface
 * @package Aheadworks\Ca\Model\Email\VariableProcessor
 */
interface VariableProcessorInterface
{
    /**
     * Prepare variables before send
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables);
}
