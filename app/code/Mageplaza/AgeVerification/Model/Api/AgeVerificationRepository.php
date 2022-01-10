<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Model\Api;

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\AgeVerification\Api\AgeVerificationRepositoryInterface;
use Mageplaza\AgeVerification\Api\Data\ConfigsInterface;
use Mageplaza\AgeVerification\Api\Data\ConfigsInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\DesignConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\GeneralConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\PageConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\PurchaseConfigInterfaceFactory;
use Mageplaza\AgeVerification\Helper\Data;

/**
 * Class AgeVerificationRepository
 * @package Mageplaza\AgeVerification\Model\Api
 */
class AgeVerificationRepository implements AgeVerificationRepositoryInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var GeneralConfigInterfaceFactory
     */
    private $generalConfigFactory;

    /**
     * @var PageConfigInterfaceFactory
     */
    private $pageConfigFactory;

    /**
     * @var PurchaseConfigInterfaceFactory
     */
    private $purchaseConfigFactory;

    /**
     * @var DesignConfigInterfaceFactory
     */
    private $designConfigFactory;

    /**
     * @var ConfigsInterfaceFactory
     */
    private $configsFactory;

    /**
     * AgeVerificationRepository constructor.
     *
     * @param Data $helperData
     * @param GeneralConfigInterfaceFactory $generalConfigFactory
     * @param PageConfigInterfaceFactory $pageConfigFactory
     * @param PurchaseConfigInterfaceFactory $purchaseConfigFactory
     * @param DesignConfigInterfaceFactory $designConfigFactory
     * @param ConfigsInterfaceFactory $configsFactory
     */
    public function __construct(
        Data $helperData,
        GeneralConfigInterfaceFactory $generalConfigFactory,
        PageConfigInterfaceFactory $pageConfigFactory,
        PurchaseConfigInterfaceFactory $purchaseConfigFactory,
        DesignConfigInterfaceFactory $designConfigFactory,
        ConfigsInterfaceFactory $configsFactory
    ) {
        $this->helperData = $helperData;
        $this->generalConfigFactory = $generalConfigFactory;
        $this->pageConfigFactory = $pageConfigFactory;
        $this->purchaseConfigFactory = $purchaseConfigFactory;
        $this->designConfigFactory = $designConfigFactory;
        $this->configsFactory = $configsFactory;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getConfigs()
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('Module is disabled'));
        }

        $generalConfig = $this->generalConfigFactory->create(['data' => $this->helperData->getGeneralConfigData()]);
        $pageConfig = $this->pageConfigFactory->create([
            'data' => $this->helperData->getPageConfigData(null, true)
        ]);
        $purchaseConfig = $this->helperData->isEnablePurchase()
            ? $this->purchaseConfigFactory->create(['data' => $this->helperData->getPurchaseConfigData(null, true)])
            : null;
        $designConfig = $this->designConfigFactory->create(['data' => $this->helperData->getDesignConfigData()]);

        $configs = $this->configsFactory->create([
            'data' => [
                ConfigsInterface::GENERAL => $generalConfig,
                ConfigsInterface::PAGE_VERIFY => $pageConfig,
                ConfigsInterface::PURCHASE_VERIFY => $purchaseConfig,
                ConfigsInterface::DESIGN => $designConfig
            ]
        ]);

        return $configs;
    }
}
