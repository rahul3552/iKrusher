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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Source\QuickOrder\OperationResult;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MessageType
 *
 * @package Aheadworks\QuickOrder\Model\Source\QuickOrder\OperationResult
 */
class MessageType implements OptionSourceInterface
{
    /**#@+
     * Message types
     */
    const SUCCESS = 'success';
    const ERROR = 'error';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SUCCESS, 'label' => __('Success')],
            ['value' => self::ERROR, 'label' => __('Error')]
        ];
    }
}
