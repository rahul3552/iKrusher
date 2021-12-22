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
namespace Aheadworks\Ctq\Model\Source\History;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\Ctq\Model\Source\History
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Constants defined for history status
     */
    const CREATED_QUOTE = 'created_quote';
    const UPDATED_QUOTE = 'updated_quote';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CREATED_QUOTE, 'label' => __('Created Quote')],
            ['value' => self::UPDATED_QUOTE, 'label' => __('Updated Quote')]
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

    /**
     * Retrieve options
     *
     * @param string $code
     * @return string|null
     */
    public function getOptionByCode($code)
    {
        foreach ($this->toOptionArray() as $optionItem) {
            if ($optionItem['value'] == $code) {
                return $optionItem['label'];
            }
        }
        return null;
    }
}
