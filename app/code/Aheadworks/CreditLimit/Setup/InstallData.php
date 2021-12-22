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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\CreditLimit\Model\Customer\Backend\BookmarkInstaller;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class InstallData
 *
 * @package Aheadworks\CreditLimit\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BookmarkInstaller
     */
    private $bookmarkInstaller;

    /**
     * @param BookmarkInstaller $bookmarkInstaller
     */
    public function __construct(
        BookmarkInstaller $bookmarkInstaller
    ) {
        $this->bookmarkInstaller = $bookmarkInstaller;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->bookmarkInstaller->install();
    }
}
