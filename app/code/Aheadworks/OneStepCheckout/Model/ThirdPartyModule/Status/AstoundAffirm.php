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
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class AstoundAffirm
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class AstoundAffirm
{
    const MODULE_NAME = 'Astound_Affirm';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleList->has(self::MODULE_NAME);
    }
}
