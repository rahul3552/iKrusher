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
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * @package Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate
 */
class Time implements OptionSourceInterface
{
    const HOURS_STEP = 1;
    const MINUTES_STEP = 30;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatter;

    /**
     * @var array
     */
    private $options;

    /**
     * @param TimezoneInterface $localeDate
     * @param DateTimeFormatterInterface $dateTimeFormatter
     */
    public function __construct(
        TimezoneInterface $localeDate,
        DateTimeFormatterInterface $dateTimeFormatter
    ) {
        $this->localeDate = $localeDate;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];
            for ($hour = 0; $hour < 24; $hour = $hour + self::HOURS_STEP) {
                for ($minute = 0; $minute < 60; $minute = $minute + self::MINUTES_STEP) {
                    $value = $hour * 60 * 60 + $minute * 60;
                    $label = $this->dateTimeFormatter->formatObject(
                        (new \DateTime(null))->setTimestamp($value),
                        $this->localeDate->getTimeFormat(\IntlDateFormatter::SHORT)
                    );

                    $this->options[] = ['value' => $value, 'label' => $label];
                }
            }
        }
        return $this->options;
    }
}
