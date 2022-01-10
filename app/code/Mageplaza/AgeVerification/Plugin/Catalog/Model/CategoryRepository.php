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

use Magento\Catalog\Api\Data\CategoryExtensionInterfaceFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\CategoryRepository as CoreCategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AgeVerification\Api\Data\ConfigsInterface;
use Mageplaza\AgeVerification\Api\Data\ConfigsInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\DesignConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\GeneralConfigInterfaceFactory;
use Mageplaza\AgeVerification\Api\Data\PageConfigInterfaceFactory;
use Mageplaza\AgeVerification\Block\Action;
use Mageplaza\AgeVerification\Helper\Data;

/**
 * Class CategoryRepository
 * @package Mageplaza\AgeVerification\Plugin\Catalog\Model
 */
class CategoryRepository
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CategoryExtensionInterfaceFactory
     */
    private $categoryExtensionFactory;

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
     * @var ConfigsInterfaceFactory
     */
    private $configsFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * CategoryRepository constructor.
     *
     * @param Data $helperData
     * @param CategoryExtensionInterfaceFactory $categoryExtensionFactory
     * @param GeneralConfigInterfaceFactory $generalConfigFactory
     * @param PageConfigInterfaceFactory $pageConfigFactory
     * @param DesignConfigInterfaceFactory $designConfigFactory
     * @param ConfigsInterfaceFactory $configsFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helperData,
        CategoryExtensionInterfaceFactory $categoryExtensionFactory,
        GeneralConfigInterfaceFactory $generalConfigFactory,
        PageConfigInterfaceFactory $pageConfigFactory,
        DesignConfigInterfaceFactory $designConfigFactory,
        ConfigsInterfaceFactory $configsFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->helperData = $helperData;
        $this->categoryExtensionFactory = $categoryExtensionFactory;
        $this->generalConfigFactory = $generalConfigFactory;
        $this->designConfigFactory = $designConfigFactory;
        $this->pageConfigFactory = $pageConfigFactory;
        $this->configsFactory = $configsFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param CoreCategoryRepository $subject
     * @param CategoryInterface $result
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterGet(CoreCategoryRepository $subject, $result)
    {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        $appliedCategories = explode(',', $this->helperData->getCategoryConfig());

        $categoryId = $result->getId();
        $defaultCatId = $this->storeManager->getStore()->getRootCategoryId();

        $extensionAttributes = $result->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->categoryExtensionFactory->create();
        }

        if (!(in_array(Action::ALL_CATEGORY_ID, $appliedCategories, true)
            || in_array($defaultCatId, $appliedCategories, true)
            || in_array($categoryId, $appliedCategories, false))
        ) {
            $extensionAttributes->setMpAgeVerification(false);
            $result->setExtensionAttributes($extensionAttributes);

            return $result;
        }

        $extensionAttributes->setMpAgeVerification(true);

        $generalConfig = $this->generalConfigFactory->create(['data' => $this->helperData->getGeneralConfigData()]);
        $pageConfig = $this->pageConfigFactory->create(['data' => $this->helperData->getPageConfigData()]);
        $designConfig = $this->designConfigFactory->create(['data' => $this->helperData->getDesignConfigData()]);

        $configs = $this->configsFactory->create([
            'data' => [
                ConfigsInterface::GENERAL => $generalConfig,
                ConfigsInterface::PAGE_VERIFY => $pageConfig,
                ConfigsInterface::DESIGN => $designConfig
            ]
        ]);

        $extensionAttributes->setMpAgeVerificationConfig($configs);
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
