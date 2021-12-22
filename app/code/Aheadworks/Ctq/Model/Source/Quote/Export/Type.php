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
namespace Aheadworks\Ctq\Model\Source\Quote\Export;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\Ctq\Model\Source\Quote\Action
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Constants defined for quote export file types
     */
    const DOC = 'doc';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DOC, 'label' => __('Doc')]
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
