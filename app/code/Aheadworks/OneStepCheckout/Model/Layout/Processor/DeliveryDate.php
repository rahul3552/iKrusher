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
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\DisplayOption as DisplayOptionSource;
use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\TimeSlot as TimeSlotSource;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class DeliveryDate
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class DeliveryDate implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var TimeSlotSource
     */
    private $timeSlotSource;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ArrayManager $arrayManager
     * @param TimeSlotSource $timeSlotSource
     * @param Config $config
     */
    public function __construct(
        ArrayManager $arrayManager,
        TimeSlotSource $timeSlotSource,
        Config $config
    ) {
        $this->arrayManager = $arrayManager;
        $this->timeSlotSource = $timeSlotSource;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->config->getDeliveryDateDisplayOption() != DisplayOptionSource::NO) {
            $deliveryDatePath = 'components/checkout/children/shippingMethod/children/delivery-date/'
                . 'children/delivery-date-fieldset/children';

            $deliveryDateLayout = $this->arrayManager->get($deliveryDatePath, $jsLayout);
            if ($deliveryDateLayout) {
                if ($this->config->getDeliveryDateDisplayOption() == DisplayOptionSource::DATE_AND_TIME) {
                    $deliveryDateLayout = $this->addTimeOptions($deliveryDateLayout);
                } else {
                    $deliveryDateLayout = $this->hideTime($deliveryDateLayout);
                }
                $jsLayout = $this->arrayManager->set($deliveryDatePath, $jsLayout, $deliveryDateLayout);
            }
        }
        return $jsLayout;
    }

    /**
     * Add delivery time options
     *
     * @param array $layout
     * @return array
     */
    private function addTimeOptions(array $layout)
    {
        if (isset($layout['time'])) {
            $timeSlotOptions = $this->timeSlotSource->toOptionArray();
            $layout['time']['options'] = $timeSlotOptions;
            $layout['time']['visible'] = !empty($timeSlotOptions);
        }
        return $layout;
    }

    /**
     * Hide time input
     *
     * @param array $layout
     * @return array
     */
    private function hideTime(array $layout)
    {
        if (isset($layout['time'])) {
            $layout['time']['visible'] = false;
        }
        return $layout;
    }
}
