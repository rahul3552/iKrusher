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

namespace Mageplaza\AgeVerification\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AgeVerification\Api\Data\DesignConfigInterface;
use Mageplaza\AgeVerification\Api\Data\GeneralConfigInterface;
use Mageplaza\AgeVerification\Api\Data\PageConfigInterface;
use Mageplaza\AgeVerification\Api\Data\PurchaseConfigInterface;
use Mageplaza\AgeVerification\Model\ConditionFactory;
use Mageplaza\AgeVerification\Model\PurchaseConditionFactory;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\AgeVerification\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpageverify';

    /**
     * @var ConditionFactory
     */
    protected $conditionFactory;

    /**
     * @var PurchaseConditionFactory
     */
    protected $purchaseConditionFactory;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param ConditionFactory $conditionFactory
     * @param PurchaseConditionFactory $purchaseConditionFactory
     * @param Image $imageHelper
     * @param Repository $assetRepo
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        ConditionFactory $conditionFactory,
        PurchaseConditionFactory $purchaseConditionFactory,
        Image $imageHelper,
        Repository $assetRepo
    ) {
        $this->conditionFactory = $conditionFactory;
        $this->purchaseConditionFactory = $purchaseConditionFactory;
        $this->imageHelper = $imageHelper;
        $this->assetRepo = $assetRepo;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getCustomerGroupsConfig($scopeId = null)
    {
        return $this->getConfigGeneral('customer_groups', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getTermTitle($scopeId = null)
    {
        return $this->getConfigGeneral('link_term', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getAnchorText($scopeId = null)
    {
        return $this->getConfigGeneral('anchor_text', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed|string
     */
    public function getAnchorUrl($scopeId = null)
    {
        $url = $this->getConfigGeneral('anchor_url', $scopeId);

        return $url ?: '#';
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPageVerifyConfig($code, $storeId = null)
    {
        return $this->getModuleConfig('page_verify/' . $code, $storeId);
    }

    /**
     * @return bool
     */
    public function isAutoVerify()
    {
        return (bool)$this->getConfigGeneral('auto_verify');
    }

    /**
     * @param int|string|null $storeId
     *
     * @return mixed
     */
    public function getAgeVerify($storeId = null)
    {
        return $this->getConfigGeneral('verify_age', $storeId);
    }

    /**
     * @param null $scopeId
     *
     * @return array
     */
    public function getApplyFor($scopeId = null)
    {
        $config = $this->getPageVerifyConfig('apply_for', $scopeId);

        return explode(',', $config);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getApplyForCms($scopeId = null)
    {
        return $this->getPageVerifyConfig('apply_for_cms', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getCategoryConfig($scopeId = null)
    {
        return $this->getPageVerifyConfig('apply_for_category', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return bool
     */
    public function isEnableConditionPage($scopeId = null)
    {
        return (bool)$this->getPageVerifyConfig('enabled_condition', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getExcludePages($scopeId = null)
    {
        return $this->getPageVerifyConfig('exclude_pages', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getRedirectUrl($scopeId = null)
    {
        return $this->getConfigGeneral('redirect', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getIncludePages($scopeId = null)
    {
        return $this->getPageVerifyConfig('include_pages', $scopeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPurchaseConfig($code, $storeId = null)
    {
        return $this->getModuleConfig('purchase_verify/' . $code, $storeId);
    }

    /**
     * @param int|string|null $storeId
     *
     * @return bool
     */
    public function isEnablePurchase($storeId = null)
    {
        return (bool)$this->getPurchaseConfig('enabled', $storeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getPageCondition($scopeId = null)
    {
        return $this->getPageVerifyConfig('condition', $scopeId);
    }

    /**
     * @param null $id
     * @param null $scope
     *
     * @return array|mixed
     */
    public function getPurchaseCondition($id = null, $scope = null)
    {
        return $this->getConfigValue('mpageverify/purchase_verify/condition', $id, $scope);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getTypeNotice($scopeId = null)
    {
        return $this->getPurchaseConfig('notice_type', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getImageNotice($scopeId = null)
    {
        return $this->getPurchaseConfig('image', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return Phrase|mixed
     */
    public function getNoticeMessage($scopeId = null)
    {
        $message = $this->getPurchaseConfig('message', $scopeId);

        return $message ?: __('You must verify your age to buy this product.');
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDesignConfig($code, $storeId = null)
    {
        return $this->getModuleConfig('design/' . $code, $storeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getVerifyType($scopeId = null)
    {
        return $this->getDesignConfig('verify_type', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getTitle($scopeId = null)
    {
        return $this->getDesignConfig('title', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getLogoPopup($scopeId = null)
    {
        return $this->getDesignConfig('image', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getDescription($scopeId = null)
    {
        return $this->getDesignConfig('description', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getConfirmLabel($scopeId = null)
    {
        return $this->getDesignConfig('confirm_label', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getCancelLabel($scopeId = null)
    {
        return $this->getDesignConfig('cancel_label', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getTitleBg($scopeId = null)
    {
        return $this->getDesignConfig('title_bg', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getContentBg($scopeId = null)
    {
        return $this->getDesignConfig('content_bg', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getButtonColor($scopeId = null)
    {
        return $this->getDesignConfig('button_color', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getTextColor($scopeId = null)
    {
        return $this->getDesignConfig('text_color', $scopeId);
    }

    /**
     * @param int|string|null $storeId
     *
     * @return array|null
     */
    public function getPageMatchingProductIds($storeId = null)
    {
        $condition = $this->getPageCondition($storeId);

        return $this->conditionFactory->create()->getMatchingProductIds($condition);
    }

    /**
     * @param int|string|null $storeId
     *
     * @return array|null
     */
    public function getPurchaseMatchingProductIds($storeId = null)
    {
        $condition = $this->getPurchaseCondition($storeId);

        return $this->purchaseConditionFactory->create()->getMatchingProductIds($condition);
    }

    /**
     * @param int|string|null $storeId
     *
     * @return array
     */
    public function getGeneralConfigData($storeId = null)
    {
        $customerGroups = explode(',', (string)$this->getCustomerGroupsConfig($storeId));

        return [
            GeneralConfigInterface::VERIFY_AGE => $this->getAgeVerify($storeId),
            GeneralConfigInterface::ENABLED_TERM_CONDITION =>
                (bool)$this->getConfigGeneral(GeneralConfigInterface::ENABLED_TERM_CONDITION, $storeId),
            GeneralConfigInterface::LINK_TERM => $this->getTermTitle($storeId),
            GeneralConfigInterface::ANCHOR_TEXT => $this->getAnchorText($storeId),
            GeneralConfigInterface::ANCHOR_URL => $this->getAnchorUrl($storeId),
            GeneralConfigInterface::COOKIE_TIME =>
                $this->getConfigGeneral(GeneralConfigInterface::COOKIE_TIME, $storeId),
            GeneralConfigInterface::CUSTOMER_GROUPS => $customerGroups,
            GeneralConfigInterface::AUTO_VERIFY =>
                $this->getConfigGeneral(GeneralConfigInterface::AUTO_VERIFY),
            GeneralConfigInterface::REDIRECT => $this->getRedirectUrl($storeId)
        ];
    }

    /**
     * @param int|string|null $storeId
     * @param bool $isConfig
     *
     * @return array
     */
    public function getPageConfigData($storeId = null, $isConfig = false)
    {
        return [
            PageConfigInterface::APPLY_FOR => $isConfig ? $this->getApplyFor($storeId) : null,
            PageConfigInterface::APPLY_FOR_CMS =>
                $isConfig ? explode(',', (string)$this->getApplyForCms($storeId)) : null,
            PageConfigInterface::APPLY_FOR_CATEGORY =>
                $isConfig ? explode(',', (string)$this->getCategoryConfig($storeId)) : null,
            PageConfigInterface::INCLUDE_PAGES =>
                array_filter(array_map('trim', explode("\n", (string)$this->getIncludePages($storeId)))),
            PageConfigInterface::EXCLUDE_PAGES =>
                array_filter(array_map('trim', explode("\n", (string)$this->getExcludePages($storeId)))),
            PageConfigInterface::PRODUCT_IDS => $isConfig && $this->isEnableConditionPage($storeId)
                ? $this->getPageMatchingProductIds($storeId) : null
        ];
    }

    /**
     * @param int|string|null $storeId
     * @param bool $isConfig
     *
     * @return array
     */
    public function getPurchaseConfigData($storeId = null, $isConfig = false)
    {
        $isEnablePurchase = $this->isEnablePurchase($storeId);
        if (!$isEnablePurchase) {
            return [];
        }

        return [
            PurchaseConfigInterface::ENABLED => $isEnablePurchase,
            PurchaseConfigInterface::PRODUCT_IDS =>
                $isConfig ? $this->getPurchaseMatchingProductIds($storeId) : null,
            PurchaseConfigInterface::NOTICE_TYPE =>
                $this->getPurchaseConfig(PurchaseConfigInterface::NOTICE_TYPE, $storeId),
            PurchaseConfigInterface::IMAGE => $this->getImageNoticeUrl(),
            PurchaseConfigInterface::MESSAGE => $this->getNoticeMessage($storeId),
        ];
    }

    /**
     * @return string
     */
    public function getImageNoticeUrl()
    {
        if ($this->getImageNotice()) {
            try {
                return $this->imageHelper->getBaseMediaUrl() . '/' .
                    $this->imageHelper->getMediaPath($this->getImageNotice(), 'notice_image');
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return $this->getImageUrl('Mageplaza_AgeVerification::media/notice.png');
    }

    /**
     * @param string $imageId
     *
     * @return string
     */
    public function getImageUrl($imageId)
    {
        return $this->assetRepo->getUrlWithParams($imageId, ['area' => Area::AREA_FRONTEND]);
    }

    /**
     * @param int|string|null $storeId
     *
     * @return array
     */
    public function getDesignConfigData($storeId = null)
    {
        return [
            DesignConfigInterface::VERIFY_TYPE => $this->getVerifyType($storeId),
            DesignConfigInterface::IMAGE => $this->getLogoPopupUrl(),
            DesignConfigInterface::TITLE => $this->getTitle($storeId),
            DesignConfigInterface::DESCRIPTION => $this->getDescription($storeId),
            DesignConfigInterface::CONFIRM_LABEL => $this->getConfirmLabel($storeId),
            DesignConfigInterface::CANCEL_LABEL => $this->getCancelLabel($storeId),
            DesignConfigInterface::TITLE_BG => $this->getTitleBg($storeId),
            DesignConfigInterface::CONTENT_BG => $this->getContentBg($storeId),
            DesignConfigInterface::BUTTON_COLOR => $this->getButtonColor($storeId),
            DesignConfigInterface::TEXT_COLOR => $this->getTextColor($storeId),
        ];
    }

    /**
     * @return string
     */
    public function getLogoPopupUrl()
    {
        if ($this->getLogoPopup()) {
            try {
                return $this->imageHelper->getBaseMediaUrl() . '/' .
                    $this->imageHelper->getMediaPath($this->getLogoPopup(), 'icon');
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return $this->getImageUrl('Mageplaza_AgeVerification::media/notice.png');
    }
}
