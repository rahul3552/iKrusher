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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class AvailabilityChecker
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta
 */
class AvailabilityChecker
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Check if attribute is available on checkout address form
     *
     * @param AttributeMetadataInterface $attributeMeta
     * @return bool
     */
    public function isAvailableOnForm(AttributeMetadataInterface $attributeMeta)
    {
        if ($this->moduleManager->isEnabled('Magento_CustomerCustomAttributes')) {
            return true;
        }
        return !$attributeMeta->isUserDefined() && $attributeMeta->isVisible();
    }
}
