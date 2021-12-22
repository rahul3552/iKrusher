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
namespace Aheadworks\OneStepCheckout\Model\Config\Backend\Customization;

use Aheadworks\OneStepCheckout\Model\Config\Backend\Customization;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Validator
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend\Customization
 */
class Validator extends AbstractValidator
{
    /**
     * Returns true if and only if value meets the validation requirements
     *
     * @param Customization $entity
     * @return bool
     */
    public function isValid($entity)
    {
        $this->_clearMessages();

        $value = $entity->getValue();
        foreach ($value['attributes'] as $attributeConfig) {
            if (isset($attributeConfig['label'])) {
                if (!\Zend_Validate::is($attributeConfig['label'], 'NotEmpty')) {
                    $this->_addMessages(['Label is required.']);
                }
            } else {
                foreach ($attributeConfig as $attrLineConfig) {
                    if (is_array($attrLineConfig)) {
                        if (!\Zend_Validate::is($attrLineConfig['label'], 'NotEmpty')) {
                            $this->_addMessages(['Label is required.']);
                        }
                    }
                }
            }
        }

        return empty($this->getMessages());
    }
}
