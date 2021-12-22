<?php
namespace Vueai\ProductRecommendations\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Vueai\ProductRecommendations\Model\ResourceModel\Signup\CollectionFactory ;
use Vueai\ProductRecommendations\Model\SignupFactory;

class Data extends AbstractHelper
{
    /**
     * @var Http
     */
    private $httpRequest;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SignupFactory
     */
    private $signupFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param Http $httpRequest
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param SignupFactory $signupFactory
     */
    public function __construct(
        Context $context,
        Http $httpRequest,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        SignupFactory $signupFactory
    ) {
        $this->httpRequest       = $httpRequest;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager      = $storeManager;
        $this->signupFactory     = $signupFactory;
        parent::__construct($context);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEmbeddedCode()
    {
        $url = $this->httpRequest->getServer('HTTP_HOST');
        $storeId = $this->storeManager->getStore()->getId();

        $collections = $this->collectionFactory->create()->addFieldToSelect(['embedded_code','domain','store_id'])
                        ->addFieldToFilter('domain', $url)
                        ->addFieldToFilter('store_id', $storeId);
        if ($collections->getSize() > 0) {
            $embeddedcode = $collections->getFirstItem();
            return $embeddedcode->getEmbeddedCode();
        }
        return false;
    }

    /**
     * @return bool|\Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStatusData()
    {
        $url     = $this->httpRequest->getServer('HTTP_HOST');
        $storeId = $this->storeManager->getStore()->getId();

        $collections = $this->collectionFactory->create()->addFieldToSelect(['status','domain','store_id'])
                        ->addFieldToFilter('domain', $url)
                        ->addFieldToFilter('store_id', $storeId);
        return $collections->getFirstItem();
    }

    /**
     * @return bool|\Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCompanyName()
    {
        $url     = $this->httpRequest->getServer('HTTP_HOST');
        $storeId = $this->storeManager->getStore()->getId();

        $collections = $this->collectionFactory->create()->addFieldToSelect(['company','email','domain','store_id'])
            ->addFieldToFilter('domain', $url)
            ->addFieldToFilter('store_id', $storeId);
        return $collections->getFirstItem();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getApiEndPoint()
    {
        $apiUrl  = "https://app.vue.ai/api/v1/events/";
        $cuttent_url     = $this->httpRequest->getServer('HTTP_HOST');
        $storeId = $this->storeManager->getStore()->getId();
        $collections = $this->collectionFactory->create()->addFieldToSelect(['api_url','domain','store_id'])
            ->addFieldToFilter('domain', $cuttent_url)
            ->addFieldToFilter('store_id', $storeId);
        if ($collections->getFirstItem()->getApiUrl()) {
            $apiUrl = $collections->getFirstItem()->getApiUrl();
        }
        return $apiUrl;
    }
}
