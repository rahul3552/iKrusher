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
namespace Aheadworks\OneStepCheckout\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class ConfigSetup
 * @package Aheadworks\OneStepCheckout\Setup
 */
class ConfigSetup
{
    /**
     * Restore to default config values
     *
     * @param ModuleDataSetupInterface $setup
     * @param string $path
     * @return $this
     */
    public function restoreToDefault(ModuleDataSetupInterface $setup, $path)
    {
        $connection = $setup->getConnection();
        $connection->delete($setup->getTable('core_config_data'), ['path = ?' => $path]);
        return $this;
    }
}
