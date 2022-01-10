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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Mageplaza\ProductAttachments\Block\Product\Display\Inline;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\Config\Source\System\ShowOn;
use Mageplaza\ProductAttachments\Model\ResourceModel\File\Collection;

/**
 * Class Attachments
 * @package Mageplaza\ProductAttachments\Block\Product
 */
class Attachments extends View
{
    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var ShowOn
     */
    public $showOn;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Attachments constructor.
     *
     * @param Context $context
     * @param UrlEncoderInterface $urlEncoder
     * @param JsonEncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param HttpContext $httpContext
     * @param Data $helperData
     * @param ShowOn $showOn
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        HttpContext $httpContext,
        Data $helperData,
        ShowOn $showOn,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->helperData  = $helperData;
        $this->showOn      = $showOn;

        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setTabTitle();

        parent::_construct();
    }

    /**
     * set size chart product tab title
     */
    public function setTabTitle()
    {
        $title = __('Product Attachments');
        $this->setTitle($title);
    }

    /**
     * Get title of attachments block
     *
     * @return Phrase
     */
    public function getTitle()
    {
        return $this->helperData->getConfigGeneral('title') ?: __('Files');
    }

    /**
     * @param string $group
     * @param array $groupValue
     *
     * @return array|Collection
     */
    public function getFileList($group, $groupValue = [])
    {
        $product = $this->getProduct();

        if (method_exists($product, 'getId')) {
            $productId = $product->getId();

            return $this->helperData->getFileByProduct($productId, $group, true, $groupValue);
        }

        return [];
    }

    /**
     * Get show on location
     *
     * @return array
     */
    public function getDisplayTypes()
    {
        $productAttLocation = $this->getProductAttachmentLocation();
        if ($productAttLocation === null) {
            $displayType = $this->helperData->getConfigGeneral('show_on');
            if ($displayType === null) {
                return [];
            }

            return explode(',', $displayType);
        }

        return explode(',', $productAttLocation);
    }

    /**
     * @return mixed
     */
    public function getFileContentHtml()
    {
        $html = $this->_layout->createBlock(Inline::class)->toHtml();

        return Data::jsonEncode($html);
    }

    /**
     * @param string $group
     * @param array $groupValue
     *
     * @return array
     */
    public function getFileRuleList($group, $groupValue = [])
    {
        /** if the product use the current rules */
        $fileInRule = [];
        /** @var ProductModel $product */
        $product    = $this->getProduct();
        if ($product !== null) {
            $fileCollection = $this->helperData->getFileByRule($group, true, $groupValue);
            foreach ($fileCollection as $file) {
                $customerGroups = explode(',', $file->getCustomerGroup());
                if ($file->getConditions()->validate($product)
                    && in_array((string) $this->getCustomerGroup(), $customerGroups, true)
                ) {
                    $fileInRule[] = $file->getData();
                }
            }
        }

        return $fileInRule;
    }

    /**
     * @return mixed|null
     */
    public function getProductAttachmentLocation()
    {
        /** @var ProductModel $product */
        $product        = $this->getProduct();
        $attributeValue = $product->getCustomAttribute(Data::ATTACHMENTS_LOCATION_ATTRIBUTE_CODE);

        return $attributeValue !== null ? $attributeValue->getValue() : null;
    }

    /**
     * Get current customer group
     *
     * @return int|string
     */
    public function getCustomerGroup()
    {
        if ($this->isCustomerLoggedIn()) {
            return $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
        }

        return 0;
    }

    /**
     * Check if need to login to download file
     *
     * @param bool $fileCustomerLogin
     *
     * @return bool
     */
    public function isLoggedDownload($fileCustomerLogin)
    {
        if (!$fileCustomerLogin) {
            return false;
        }

        return !$this->isCustomerLoggedIn();
    }

    /**
     * @param string $fileIsBuyer
     * @param string $orderStatuses
     *
     * @return bool
     */
    public function isPurchased($fileIsBuyer, $orderStatuses)
    {
        $productId = $this->getProduct()->getId();

        return $this->helperData->isPurchased($fileIsBuyer, $orderStatuses, $productId);
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $product = $this->getProduct();
        if (method_exists($product, 'getIdentities')) {
            $identities = $product->getIdentities();
            $category   = $this->_coreRegistry->registry('current_category');
            if ($category) {
                $identities[] = Category::CACHE_TAG . '_' . $category->getId();
            }

            return $identities;
        }

        return [];
    }

    /**
     * @return mixed|null
     */
    protected function isCustomerLoggedIn()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @return mixed
     */
    public function getDisplayFileSize()
    {
        return $this->helperData->getDisplayFileSize();
    }

    /**
     * @return array
     */
    public function getAllGroups()
    {
        $options = [];
        $groups  = $this->helperData->getGroups();
        $groups  = $groups ? Data::jsonDecode($groups) : [];

        foreach ($groups as $group) {
            if (isset($group['value'])) {
                usort($group['value'], $this->helperData->sortByPosition());
                foreach ($group['value'] as $value) {
                    $options[] = [
                        'value' => $value['value'],
                        'label' => $value['name']
                    ];
                }
            }
        }

        return $options;
    }
}
