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
namespace Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DisplayOption
 * @package Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate
 */
class DisplayOption implements OptionSourceInterface
{
    /**
     * 'No' option
     */
    const NO = 0;

    /**
     * 'Date only' option
     */
    const DATE = 1;

    /**
     * 'Date and time' option
     */
    const DATE_AND_TIME = 2;

    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => self::NO,
                    'label' => __('No')
                ],
                [
                    'value' => self::DATE,
                    'label' => __('Date Only')
                ],
                [
                    'value' => self::DATE_AND_TIME,
                    'label' => __('Date and Time')
                ]
            ];
        }
        return $this->options;
    }
}
