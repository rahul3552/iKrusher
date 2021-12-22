<?php
namespace Vueai\ProductRecommendations\Block\Adminhtml\Index;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Directory\Block\Data;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;

class Signup extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var Data
     */
    private $directoryBlock;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Http
     */
    private $httpRequest;

    /**
     * Signup constructor.
     *
     * @param UrlInterface $backendUrl
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Session $authSession
     * @param Data $directoryBlock
     * @param Country $country
     * @param Registry $registry
     * @param Http $httpRequest
     * @param array $data
     */
    public function __construct(
        UrlInterface $backendUrl,
        Context $context,
        StoreManagerInterface $storeManager,
        Session $authSession,
        Data $directoryBlock,
        Country $country,
        Registry $registry,
        Http $httpRequest,
        array $data = []
    ) {
        $this->storeManager   = $storeManager;
        $this->backendUrl     = $backendUrl;
        $this->authSession    = $authSession;
        $this->directoryBlock = $directoryBlock;
        $this->country        = $country;
        $this->registry       = $registry;
        $this->httpRequest    = $httpRequest;
        parent::__construct($context, $data);
    }

    /**
     * get form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('productrecommendations/index/save');
    }

    /**
     * get mediaUrl
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->getViewFileUrl("Vueai_ProductRecommendations::images/logo.png");
    }

    /**
     * get GetStarted mediaUrl
     *
     * @return string
     */
    public function getGetStartedMediaUrl()
    {
        return $this->getViewFileUrl("Vueai_ProductRecommendations::images/Vueailogo_1.png");
    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->getFormKey();
    }

    /**
     * get logged in admin email
     *
     * @return mixed|string
     */
    public function getCurrentAdminEmail()
    {
        return $this->authSession->getUser()->getEmail();
    }

    /**
     * get logged in admin name
     *
     * @return string
     */
    public function getCurrentAdminName()
    {
        return $this->authSession->getUser()->getName();
    }

    /**
     * @return int|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        $storeID = $this->getRequest()->getParam('store');
        if (isset($storeID)) {
            return $storeID;
        }
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return mixed
     */
    public function getCurrentPageCatagory()
    {
        $category = $this->registry->registry('current_category');
        return $category->getName();
    }
}
