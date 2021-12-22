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
namespace Aheadworks\CreditLimit\Observer\System\Config;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\CreditLimit\Model\Payment\Config\UseDefaultConfigChecker;
use Aheadworks\CreditLimit\Model\Payment\Config\Backend\CustomerGroupConfig;
use Magento\Payment\Helper\Data as PaymentHelper;
use Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit\Scheduler;

/**
 * Class SaveObserver
 *
 * @package Aheadworks\CreditLimit\Observer\System\Config
 */
class SaveObserver implements ObserverInterface
{
    /**
     * @var UseDefaultConfigChecker
     */
    private $useDefaultConfigChecker;

    /**
     * @var Scheduler
     */
    private $updateScheduler;

    /**
     * @param UseDefaultConfigChecker $useDefaultConfigChecker
     * @param Scheduler $updateScheduler
     */
    public function __construct(
        UseDefaultConfigChecker $useDefaultConfigChecker,
        Scheduler $updateScheduler
    ) {
        $this->useDefaultConfigChecker = $useDefaultConfigChecker;
        $this->updateScheduler = $updateScheduler;
    }

    /**
     * Remove website config in website scope in case use default is checked
     *
     * @param Observer $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $config = $observer->getEvent()->getData('configData');
        $isUsedByDefault = $this->useDefaultConfigChecker->isUsedByDefault(
            $config,
            PaymentHelper::XML_PATH_PAYMENT_METHODS,
            CustomerGroupConfig::CONFIG_PATH
        );

        $websiteId = $config['website'] ?? null;
        if ($websiteId && $isUsedByDefault) {
            $this->updateScheduler->scheduleUpdate([], $websiteId);
        }

        return $this;
    }
}
