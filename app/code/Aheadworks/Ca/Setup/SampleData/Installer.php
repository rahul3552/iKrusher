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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Setup\SampleData;

use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;

/**
 * Class Installer
 * @package Aheadworks\Ca\Setup\SampleData
 */
class Installer implements SampleDataInstallerInterface
{
    /**
     * @var SampleDataInstallerInterface[]
     */
    private $installers;

    /**
     * @param SampleDataInstallerInterface[] $installers
     */
    public function __construct(
        array $installers = []
    ) {
        $this->installers = $installers;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        foreach ($this->installers as $installer) {
            $installer->install();
        }
    }
}
