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

namespace Mageplaza\AgeVerification\Block;

use Magento\Cms\Model\Page as CmsPage;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\AgeVerification\Helper\Data as HelperData;
use Mageplaza\AgeVerification\Helper\Image as HelperImage;
use Mageplaza\AgeVerification\Model\ConditionFactory;
use Mageplaza\AgeVerification\Model\Config\Source\ApplyFor;
use Mageplaza\AgeVerification\Model\Config\Source\TypeNotice;
use Mageplaza\AgeVerification\Model\Config\Source\VerifyType;
use Mageplaza\AgeVerification\Model\PurchaseConditionFactory as PurchaseFactory;

/**
 * Class Action
 * @package Mageplaza\AgeVerification\Block
 */
class Action extends Template
{
    const ALL_CATEGORY_ID = '0';

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var CmsPage
     */
    protected $_cmsPage;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var HelperImage
     */
    protected $_helperImage;

    /**
     * @var SessionFactory
     */
    protected $_sessionFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var HttpContext
     */
    protected $_context;

    /**
     * @var Http
     */
    protected $_response;

    /**
     * @var ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var PurchaseFactory
     */
    protected $_purchaseFactory;

    /**
     * Action constructor.
     *
     * @param Template\Context $context
     * @param HelperData $helperData
     * @param CmsPage $cmsPage
     * @param Registry $registry
     * @param HelperImage $helperImage
     * @param SessionFactory $sessionFactory
     * @param Session $customerSession
     * @param HttpContext $httpContext
     * @param Http $response
     * @param ConditionFactory $conditionFactory
     * @param PurchaseFactory $purchaseFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperData $helperData,
        CmsPage $cmsPage,
        Registry $registry,
        HelperImage $helperImage,
        SessionFactory $sessionFactory,
        Session $customerSession,
        HttpContext $httpContext,
        Http $response,
        ConditionFactory $conditionFactory,
        PurchaseFactory $purchaseFactory,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_cmsPage = $cmsPage;
        $this->_registry = $registry;
        $this->_helperImage = $helperImage;
        $this->_sessionFactory = $sessionFactory;
        $this->_customerSession = $customerSession;
        $this->_context = $httpContext;
        $this->_response = $response;
        $this->_conditionFactory = $conditionFactory;
        $this->_purchaseFactory = $purchaseFactory;

        parent::__construct($context, $data);
    }

    /**
     * Check Apply For
     *
     * @return bool
     */
    public function checkActionPage()
    {
        $config = $this->_helperData->getApplyFor();
        $action = $this->getRequest()->getFullActionName();
        $searchPage = ['catalogsearch_advanced_result'];

        if (in_array($action, $searchPage, true) && in_array(ApplyFor::CATALOG_SEARCH_PAGE, $config, true)) {
            return true;
        }

        $checkoutPages = ['onestepcheckout_index_index'];

        if (in_array($action, $checkoutPages, true) && in_array(ApplyFor::CHECKOUT_PAGE, $config, true)) {
            return true;
        }

        return in_array($action, $config, true);
    }

    /**
     * @return bool
     */
    public function checkCmsPage()
    {
        $config = explode(',', $this->_helperData->getApplyForCms());
        $cmsPage = $this->_cmsPage->getIdentifier();

        return in_array($cmsPage, $config, true);
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkCategoryPages()
    {
        $config = explode(',', $this->_helperData->getCategoryConfig());
        $currentCategory = $this->_registry->registry('current_category');

        if ($currentCategory) {
            $catId = $currentCategory->getId();
            $defaultCatId = $this->_storeManager->getStore()->getRootCategoryId();

            if (in_array($defaultCatId, $config, true) || in_array(self::ALL_CATEGORY_ID, $config, true)) {
                return true;
            }

            return in_array($catId, $config, true);
        }

        return false;
    }

    /**
     * Check Apply for Custom Page = include paths
     *
     * @return bool
     */
    public function checkIncludePaths()
    {
        $includePaths = $this->_helperData->getIncludePages();

        return $this->checkPaths($includePaths);
    }

    /**
     * Check Apply for Custom Page = exclude paths
     *
     * @return bool
     */
    public function checkExcludePaths()
    {
        $excludePaths = $this->_helperData->getExcludePages();

        return $this->checkPaths($excludePaths);
    }

    /**
     * @param $paths
     *
     * @return bool
     */
    public function checkPaths($paths)
    {
        if ($paths) {
            $currentPath = $this->getRequest()->getRequestUri();

            $arrayPaths = explode("\n", $paths);
            $pathsUrl = array_map('trim', $arrayPaths);

            foreach ($pathsUrl as $path) {
                if ($path &&
                    (strpos($currentPath, $path) !== false || $this->checkRegularExpression($path, $currentPath))
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $pattern
     * @param $currentPath
     *
     * @return bool
     */
    public function checkRegularExpression($pattern, $currentPath)
    {
        $start = substr($pattern, 0, 1);
        $end = substr($pattern, -1);
        $pos = strpos($pattern, '\/');

        if ($pos === false) {
            $pattern = '/' . str_replace('/', '\/', substr($pattern, 1, -1)) . '/';
        }

        return $start === '/' && $end === '/' && preg_match($pattern, $currentPath);
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkPageVerify()
    {
        if ($this->checkCustomerGroups() && !$this->checkPurchaseVerify() && !$this->checkExcludePaths()) {
            if ($this->checkActionPage() || $this->checkCmsPage() ||
                $this->checkIncludePaths() || $this->checkCategoryPages() || $this->checkDetailPageVerify()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function checkDetailPageVerify()
    {
        if ($this->_helperData->isEnableConditionPage()) {
            $condition = $this->_helperData->getPageCondition();

            return $this->checkDetailPage($condition, 'page');
        }

        return false;
    }

    /**
     * @return bool
     */
    public function checkPurchaseVerify()
    {
        if ($this->_helperData->isEnablePurchase() && $this->checkCustomerGroups()) {
            $condition = $this->_helperData->getPurchaseCondition(
                $this->_storeManager->getStore()->getId(),
                ScopeInterface::SCOPE_STORE
            );

            return $this->checkDetailPage($condition, 'purchase');
        }

        return false;
    }

    /**
     * @param $condition
     * @param $type
     *
     * @return bool
     */
    public function checkDetailPage($condition, $type)
    {
        $action = $this->getRequest()->getFullActionName();

        if ($action === 'catalog_product_view') {
            $product = $this->_registry->registry('current_product');

            if ($type === 'page') {
                $productIds = $this->_conditionFactory->create()->getMatchingProductIds($condition);
            } else {
                $productIds = $this->_purchaseFactory->create()->getMatchingProductIds($condition);
            }

            return in_array($product->getId(), $productIds, true);
        }

        return false;
    }

    /**
     * @return bool|Http|HttpInterface
     * @throws NoSuchEntityException
     */
    public function autoVerify()
    {
        $isCustomerLoggedIn = $this->_context->getValue(Context::CONTEXT_AUTH);

        if ($isCustomerLoggedIn && $this->_helperData->isAutoVerify()
            && ($this->checkPageVerify() || $this->checkPurchaseVerify())
        ) {
            $customer = $this->_sessionFactory->create();
            $dob = $customer->getCustomerData()->getDob();
            $url = $this->_helperData->getRedirectUrl();

            if ($dob) {
                $age = $this->_helperData->getAgeVerify();
                $setDate = $this->setDate($age);

                if ($setDate >= $dob) {
                    return true;
                }

                if ($this->checkPurchaseVerify()) {
                    return false;
                }

                //Redirect if dob less than configAge
                return $this->_response->setRedirect($url);
            }
        }

        return false;
    }

    /**
     *
     * @param $age
     *
     * @return false|string
     */
    public function setDate($age)
    {
        return date('Y-m-d', strtotime('-' . $age . ' years'));
    }

    /**
     * @return bool
     */
    public function checkCustomerGroups()
    {
        $config = $this->_helperData->getCustomerGroupsConfig();
        $customerGroup = 0;

        if ($this->_sessionFactory->create()->isLoggedIn()) {
            $customerGroup = $this->_sessionFactory->create()->getCustomer()->getGroupId();
        }

        return in_array((string)$customerGroup, explode(',', $config), true);
    }

    /**
     * @return bool
     */
    public function isEnableTerm()
    {
        return (bool)$this->_helperData->getConfigGeneral('enabled_term_condition');
    }

    /**
     * @return bool
     */
    public function isNoticeMessage()
    {
        return $this->checkPurchaseVerify() && ($this->_helperData->getTypeNotice() === TypeNotice::NOTICE_MESSAGE);
    }

    /**
     * @return bool
     */
    public function isImageMessage()
    {
        return $this->checkPurchaseVerify() && ($this->_helperData->getTypeNotice() === TypeNotice::SMALL_IMAGE);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageNoticeUrl()
    {
        if ($this->_helperData->getImageNotice()) {
            return $this->_helperImage->getBaseMediaUrl() . '/' .
                $this->_helperImage->getMediaPath($this->_helperData->getImageNotice(), 'notice_image');
        }

        return $this->getUrlImg('notice.png');
    }

    /**
     * @param $img
     *
     * @return string
     */
    public function getUrlImg($img)
    {
        return $this->getViewFileUrl('Mageplaza_AgeVerification::media/' . $img);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLogoPopupUrl()
    {
        if ($this->_helperData->getLogoPopup()) {
            return $this->_helperImage->getBaseMediaUrl() . '/' .
                $this->_helperImage->getMediaPath($this->_helperData->getLogoPopup(), 'icon');
        }

        return $this->getUrlImg('notice.png');
    }

    /**
     * @return bool
     */
    public function isCheckboxType()
    {
        $type = $this->_helperData->getVerifyType();

        return (int)$type === VerifyType::CHECKBOX;
    }

    /**
     * @return bool
     */
    public function isDOBType()
    {
        $type = $this->_helperData->getVerifyType();

        return (int)$type === VerifyType::INPUT_DOB;
    }

    /**
     * @return array|null
     */
    public function getPurchaseProductIds()
    {
        $productIds = [];

        if ($this->checkCustomerGroups()) {
            $condition = $this->_helperData->getPurchaseCondition(
                $this->_storeManager->getStore()->getId(),
                ScopeInterface::SCOPE_STORE
            );
            $productIds = $this->_purchaseFactory->create()->getMatchingProductIds($condition);
        }

        return $productIds;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPopupData()
    {
        $data = [
            'productIds' => $this->getPurchaseProductIds(),
            'age' => $this->_helperData->getConfigGeneral('verify_age'),
            'isTerm' => $this->isEnableTerm(),
            'cookieTime' => $this->getCookieTime(),
            'redirectUrl' => $this->_helperData->getRedirectUrl(),
            'isVerifyPage' => $this->checkPageVerify(),
            'isEnablePurchase' => $this->_helperData->isEnablePurchase(),
            'isVerifyPurchase' => $this->checkPurchaseVerify(),
            'autoVerify' => $this->autoVerify(),
            'yesNoType' => VerifyType::YESNO,
            'dobType' => VerifyType::INPUT_DOB,
            'checkboxType' => VerifyType::CHECKBOX
        ];

        return HelperData::jsonEncode($data);
    }

    /**
     * @return int|mixed
     */
    public function getCookieTime()
    {
        $time = $this->_helperData->getConfigGeneral('cookie_time');

        return $time ?: 365;
    }

    /**
     * @return Phrase|mixed
     */
    public function getTitlePopup()
    {
        $title = $this->_helperData->getTitle();

        return $title ?: __('AGE VERIFICATION');
    }

    /**
     * @return Phrase|mixed
     */
    public function getDescriptionPopup()
    {
        $age = $this->_helperData->getConfigGeneral('verify_age');
        $des = str_replace('{{age}}', $age, $this->_helperData->getDescription());

        return $des ?: __('You must be older than %1 years old to enter this page.', $age);
    }

    /**
     * @return Phrase|mixed
     */
    public function getConfirmLabel()
    {
        $label = $this->_helperData->getConfirmLabel();

        return $label ?: __('Enter');
    }

    /**
     * @return Phrase|mixed
     */
    public function getCancelLabel()
    {
        $label = $this->_helperData->getCancelLabel();

        return $label ?: __('Cancel');
    }
}
