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
namespace Aheadworks\OneStepCheckout\Model\Page;

use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Page\Initializer\ThirdPartyModuleList;
use Magento\Framework\View\Result\Page;

/**
 * Class Initializer
 * @package Aheadworks\OneStepCheckout\Model\Page
 */
class Initializer
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ThirdPartyModuleList
     */
    private $thirdPartyModuleList;

    /**
     * @param Config $config
     * @param ThirdPartyModuleList $thirdPartyModuleList
     */
    public function __construct(
        Config $config,
        ThirdPartyModuleList $thirdPartyModuleList
    ) {
        $this->config = $config;
        $this->thirdPartyModuleList = $thirdPartyModuleList;
    }

    /**
     * Init checkout page
     *
     * @param Page $page
     * @return void
     */
    public function init(Page $page)
    {
        foreach ($this->thirdPartyModuleList->getPresentedModules() as $moduleName) {
            $handleParts = [$page->getDefaultLayoutHandle()];
            $moduleNameParts = explode('_', $moduleName);
            foreach ($moduleNameParts as $mnPart) {
                $handleParts[] = strtolower($mnPart);
            }
            $handle = implode('_', $handleParts);
            $page->addHandle($handle);
        }

        $pageConfig = $page->getConfig();
        $pageConfig->getTitle()->set($this->config->getCheckoutTitle());
    }
}
