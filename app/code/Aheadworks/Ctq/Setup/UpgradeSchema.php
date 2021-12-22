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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Setup;

use Aheadworks\Ctq\Setup\Updater\Schema\Updater as SchemaUpdater;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\OnSale\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var SchemaUpdater
     */
    private $updater;

    /**
     * @param SchemaUpdater $updater
     */
    public function __construct(
        SchemaUpdater $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->updater->update130($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->updater->update140($setup);
        }

        $setup->endSetup();
    }
}
