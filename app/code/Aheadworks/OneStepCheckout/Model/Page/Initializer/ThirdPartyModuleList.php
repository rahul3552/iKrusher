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
namespace Aheadworks\OneStepCheckout\Model\Page\Initializer;

use Magento\Framework\Module\ModuleList;

/**
 * Class ThirdPartyModuleList
 * @package Aheadworks\OneStepCheckout\Model\Page\Initializer
 */
class ThirdPartyModuleList
{
    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * @var string[]
     */
    private $modules = [];

    /**
     * @var string[]
     */
    private $presentedModules;

    /**
     * @param ModuleList $moduleList
     * @param array $modules
     */
    public function __construct(
        ModuleList $moduleList,
        array $modules = []
    ) {
        $this->moduleList = $moduleList;
        $this->modules = $modules;
    }

    /**
     * Get presented third party modules list
     *
     * @return string[]
     */
    public function getPresentedModules()
    {
        if (!$this->presentedModules) {
            $this->presentedModules = [];
            foreach ($this->modules as $moduleName) {
                if ($this->moduleList->has($moduleName)) {
                    $this->presentedModules[] = $moduleName;
                }
            }
        }
        return $this->presentedModules;
    }
}
