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

namespace Mageplaza\AgeVerification\Plugin\Catalog\Model;

use Magento\Catalog\Api\Data\ProductExtensionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Model\ProductRepository as CoreProductRepository;
use Mageplaza\AgeVerification\Api\Data\ConfigsInterface;
use Mageplaza\AgeVerification\Api\Data\ConfigsInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\DesignConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\GeneralConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\PageConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\PurchaseConfigInterfaceFactory;
use Mageplaza\AgeVerification\Helper\Data;

/**
 * Class ProductRepository
 * @package Mageplaza\AgeVerification\Plugin\Catalog\Model
 */
class ProductRepository
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ProductExtensionInterfaceFactory
     */
    private $productExtensionFactory;

    /**
     * @var GeneralConfigInterfaceFactory
     */
    private $generalConfigFactory;

    /**
     * @var DesignConfigInterfaceFactory
     */
    private $designConfigFactory;

    /**
     * @var PageConfigInterfaceFactory
     */
    private $pageConfigFactory;

    /**
     * @var PurchaseConfigInterfaceFactory
     */
    private $purchaseConfigFactory;

    /**
     * @var ConfigsInterfaceFactory
     */
    private $configsFactory;

    /**
     * ProductRepository constructor.
     *
     * @param Data $helperData
     * @param ProductExtensionInterfaceFactory $productExtensionFactory
     * @param GeneralConfigInterfaceFactory $generalConfigFactory
     * @param PageConfigInterfaceFactory $pageConfigFactory
     * @param PurchaseConfigInterfaceFactory $purchaseConfigFactory
     * @param DesignConfigInterfaceFactory $designConfigFactory
     * @param ConfigsInterfaceFactory $configsFactory
     */
    public function __construct(
        Data $helperData,
        ProductExtensionInterfaceFactory $productExtensionFactory,
        GeneralConfigInterfaceFactory $generalConfigFactory,
        PageConfigInterfaceFactory $pageConfigFactory,
        PurchaseConfigInterfaceFactory $purchaseConfigFactory,
        DesignConfigInterfaceFactory $designConfigFactory,
        ConfigsInterfaceFactory $configsFactory
    ) {
        $this->helperData = $helperData;
        $this->productExtensionFactory = $productExtensionFactory;
        $this->generalConfigFactory = $generalConfigFactory;
        $this->designConfigFactory = $designConfigFactory;
        $this->pageConfigFactory = $pageConfigFactory;
        $this->purchaseConfigFactory = $purchaseConfigFactory;
        $this->configsFactory = $configsFactory;
    }

    /**
     * @param CoreProductRepository $subject
     * @param ProductSearchResultsInterface $result
     *
     * @return ProductSearchResultsInterface
     */
    public function afterGetList(CoreProductRepository $subject, $result)
    {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        $matchingProductIds = [];
        if ($this->helperData->isEnableConditionPage()) {
            $matchingProductIds = (array)$this->helperData->getPageMatchingProductIds();
        }

        $matchingPurchaseProductIds = [];
        if ($this->helperData->isEnablePurchase()) {
            $matchingPurchaseProductIds = (array)$this->helperData->getPurchaseMatchingProductIds();
        }

        $productIds = array_merge($matchingProductIds, $matchingPurchaseProductIds);

        foreach ($result->getItems() as $item) {
            $extensionAttributes = $item->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->productExtensionFactory->create();
            }

            if (in_array($item->getId(), $productIds, false)) {
                $extensionAttributes->setMpAgeVerification(true);
            } else {
                $extensionAttributes->setMpAgeVerification(false);
            }
        }

        return $result;
    }

    /**
     * @param CoreProductRepository $subject
     * @param ProductInterface $result
     *
     * @return mixed
     */
    public function afterGet(CoreProductRepository $subject, $result)
    {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        $matchingProductIds = $this->helperData->getPageMatchingProductIds();
        $matchingPurchaseProductIds = $this->helperData->getPurchaseMatchingProductIds();

        $productId = $result->getId();
        $extensionAttributes = $result->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->productExtensionFactory->create();
        }

        if ((!$this->helperData->isEnableConditionPage() || !in_array($productId, $matchingProductIds, false))
            && (!$this->helperData->isEnablePurchase() || !in_array($productId, $matchingPurchaseProductIds, false))
        ) {
            $extensionAttributes->setMpAgeVerification(false);
            $result->setExtensionAttributes($extensionAttributes);

            return $result;
        }

        $extensionAttributes->setMpAgeVerification(true);

        $generalConfig = $this->generalConfigFactory->create(['data' => $this->helperData->getGeneralConfigData()]);
        $pageConfig = null;
        $purchaseConfig = null;
        if ($this->helperData->isEnableConditionPage() && in_array($productId, $matchingProductIds, false)) {
            $pageConfig = $this->pageConfigFactory->create(['data' => $this->helperData->getPageConfigData()]);
        }
        if ($this->helperData->isEnablePurchase() && in_array($productId, $matchingPurchaseProductIds, false)) {
            $purchaseConfig = $this->purchaseConfigFactory->create([
                'data' => $this->helperData->getPurchaseConfigData()
            ]);
        }

        $designConfig = $this->designConfigFactory->create(['data' => $this->helperData->getDesignConfigData()]);

        $configs = $this->configsFactory->create([
            'data' => [
                ConfigsInterface::GENERAL => $generalConfig,
                ConfigsInterface::PAGE_VERIFY => $pageConfig,
                ConfigsInterface::PURCHASE_VERIFY => $purchaseConfig,
                ConfigsInterface::DESIGN => $designConfig
            ]
        ]);

        $extensionAttributes->setMpAgeVerificationConfig($configs);
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
