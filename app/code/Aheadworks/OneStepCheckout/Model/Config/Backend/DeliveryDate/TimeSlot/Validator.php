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
namespace Aheadworks\OneStepCheckout\Model\Config\Backend\DeliveryDate\TimeSlot;

use Aheadworks\OneStepCheckout\Model\Config\Backend\DeliveryDate\TimeSlot;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Validator
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend\DeliveryDate\TimeSlot
 */
class Validator extends AbstractValidator
{
    /**
     * Returns true if and only if entity value meets the validation requirements
     *
     * @param TimeSlot $entity
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isValid($entity)
    {
        $this->_clearMessages();

        // No validation

        return empty($this->getMessages());
    }
}
