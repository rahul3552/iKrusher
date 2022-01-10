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
use Magento\Catalog\Api\Data\CategorySearchResultsInterface;
use Magento\Catalog\Model\CategoryList as CoreCategoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AgeVerification\Block\Action;
use Mageplaza\AgeVerification\Helper\Data;

/**
 * Class CategoryList
 * @package Mageplaza\AgeVerification\Plugin\Catalog\Model
 */
class CategoryList
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * CategoryList constructor.
     *
     * @param Data $helperData
     * @param CategoryExtensionInterfaceFactory $categoryExtensionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helperData,
        CategoryExtensionInterfaceFactory $categoryExtensionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->helperData = $helperData;
        $this->categoryExtensionFactory = $categoryExtensionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param CoreCategoryList $subject
     * @param CategorySearchResultsInterface $result
     *
     * @return CategorySearchResultsInterface
     * @throws NoSuchEntityException
     */
    public function afterGetList(CoreCategoryList $subject, $result)
    {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        $appliedCategories = explode(',', $this->helperData->getCategoryConfig());
        $defaultCatId = $this->storeManager->getStore()->getRootCategoryId();

        foreach ($result->getItems() as $item) {
            $extensionAttributes = $item->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->categoryExtensionFactory->create();
            }
            if (in_array(Action::ALL_CATEGORY_ID, $appliedCategories, true)
                || in_array($defaultCatId, $appliedCategories, true)
                || in_array($item->getId(), $appliedCategories, false)
            ) {
                $extensionAttributes->setMpAgeVerification(true);
            } else {
                $extensionAttributes->setMpAgeVerification(false);
            }
        }

        return $result;
    }
}
