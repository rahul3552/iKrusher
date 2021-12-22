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
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon\StatusInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\AmazonVersionPool;

/**
 * Class Amazon
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class Amazon implements StatusInterface
{
    /**
     * Amazon Core Module
     */
    const AMAZON_MODULE_NAME = 'Amazon_Core';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var AmazonVersionPool
     */
    private $amazonVersionPool;

    /**
     * @param ModuleListInterface $moduleList
     * @param AmazonVersionPool $amazonVersionPool
     */
    public function __construct(
        ModuleListInterface $moduleList,
        AmazonVersionPool $amazonVersionPool
    ) {
        $this->moduleList = $moduleList;
        $this->amazonVersionPool = $amazonVersionPool;
    }

    /**
     * Check if current amazon version is declared in version processors
     *
     * @return bool
     */
    public function isPwaEnabled()
    {
        return $this->getProcessor()->isPwaEnabled();
    }

    /**
     * Get current Amazon module version
     *
     * @return mixed
     */
    private function getCurrentAmazonVersion()
    {
        return $this->moduleList
            ->getOne(self::AMAZON_MODULE_NAME)['setup_version'];
    }

    /**
     * Check if Amazon module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleList->has(self::AMAZON_MODULE_NAME);
    }

    /**
     * Get Amazon Version Processor
     *
     * @return StatusInterface
     */
    private function getProcessor()
    {
        return $this->amazonVersionPool->getAmazonVersionProcessor($this->getCurrentAmazonVersion());
    }
}
