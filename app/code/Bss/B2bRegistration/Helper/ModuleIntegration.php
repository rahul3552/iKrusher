<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_B2bRegistration
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\B2bRegistration\Helper;

/**
 * Class ModuleIntegration
 *
 * @package Bss\B2bRegistration\Helper
 */
class ModuleIntegration extends \Magento\Framework\App\Helper\AbstractHelper
{
    //@codingStandardsIgnoreStart
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $objectManager;

    /**
     * ModuleIntegration constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
    }

    /**
     * @return bool
     */
    public function isBssCustomerAttributesModuleEnabled()
    {
        return $this->isModuleOutputEnabled('Bss_CustomerAttributes');
    }

    /**
     * @return mixed
     */
    public function getBssCustomerAttributeHelper()
    {
        if ($this->isBssCustomerAttributesModuleEnabled()) {
            return $this->objectManager->create(
                \Bss\CustomerAttributes\Helper\B2BRegistrationIntegrationHelper::class
            );
        }
        return null;
    }

    //@codingStandardsIgnoreEnd
}
