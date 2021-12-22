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
 * Class DayOfMonth
 *
 * @package Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate
 */
class DayOfMonth implements OptionSourceInterface
{
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
            $this->options = [];
            for ($day = 1; $day < 32; $day++) {
                $this->options[] = ['value' => $day, 'label' => $day];
            }
        }
        return $this->options;
    }
}
