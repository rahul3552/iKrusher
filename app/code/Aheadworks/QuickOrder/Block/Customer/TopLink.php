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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Block\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Link as HtmlLink;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context;
use Aheadworks\QuickOrder\Api\CustomerManagementInterface;
use Aheadworks\QuickOrder\Model\Url as UrlModel;

/**
 * Class TopLink
 *
 * @package Aheadworks\QuickOrder\Block\Customer
 */
class TopLink extends HtmlLink
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @param TemplateContext $context
     * @param HttpContext $httpContext
     * @param CustomerManagementInterface $customerManagement
     * @param UrlModel $urlModel
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        HttpContext $httpContext,
        CustomerManagementInterface $customerManagement,
        UrlModel $urlModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->customerManagement = $customerManagement;
        $this->urlModel = $urlModel;
    }

    /**
     * Get url to quick order page for href attribute
     *
     * @return string
     */
    public function getHref()
    {
        return $this->urlModel->getUrlToQuickOrderPage();
    }

    /**
     * Check if need to display link
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isNeedToDisplayLink()
    {
        return $this->customerManagement->isActiveForCustomerGroup(
            $this->httpContext->getValue(Context::CONTEXT_GROUP),
            $this->_storeManager->getWebsite()->getId()
        );
    }
}
