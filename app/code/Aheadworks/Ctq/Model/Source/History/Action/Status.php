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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Source\History\Action;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Ctq\Model\Source\History\Action
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Constants defined for history action status
     */
    const CREATED = 'created';
    const UPDATED = 'updated';
    const REMOVED = 'removed';
    const RECONFIGURED = 'reconfigured';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CREATED, 'label' => __('Created')],
            ['value' => self::UPDATED, 'label' => __('Updated')],
            ['value' => self::REMOVED, 'label' => __('Removed')],
            ['value' => self::RECONFIGURED, 'label' => __('Reconfigured')]
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->toOptionArray() as $optionItem) {
            $options[$optionItem['value']] = $optionItem['label'];
        }
        return $options;
    }
}
