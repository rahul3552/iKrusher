<?php
namespace Vueai\ProductRecommendations\Observer;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Store\Model\StoreManagerInterface;
use Vueai\ProductRecommendations\Helper\Data;

class OauthConfig implements ObserverInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var ClientFactory
     */
    private $httpClientFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var Http
     */
    private $httpRequest;

    /**
     * @var Http
     */
    private $helper;

    /**
     * OauthConfig constructor.
     *
     * @param Context $context
     * @param ClientFactory $clientFactory
     * @param StoreManagerInterface $storeManager
     * @param Session $authSession
     * @param Http $httpRequest
     */
    public function __construct(
        Context $context,
        ClientFactory $clientFactory,
        StoreManagerInterface $storeManager,
        Session $authSession,
        Http $httpRequest,
        Data $helper
    ) {
        $this->context           = $context;
        $this->httpClientFactory = $clientFactory;
        $this->storeManager      = $storeManager;
        $this->authSession       = $authSession;
        $this->httpRequest       = $httpRequest;
        $this->helper            = $helper;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $oauthpdata      = $this->httpRequest->getPost('groups');
        $Oauthcollection = $oauthpdata['oauth']['fields'];
        $timeCollection  = $oauthpdata['catalog_update_frequency']['fields'];

        $time = [];
        $data = [];
        foreach ($Oauthcollection as $key => $row) {
            $data[$key] = $row['value'];
        }
        foreach ($timeCollection as $key => $row) {
            $time[$key] = $row['value'];
        }

        $domain = ['source' => 'magento',
                   'event'  => 'catalog',
                   'company'=> $this->helper->getCompanyName()->getCompany(),
                   'domain' => $this->httpRequest->getServer('HTTP_HOST'),
                   'email'  => $this->helper->getCompanyName()->getEmail(),
                   'time'   => $time['timepicker']];
        $data_string = array_merge($data, $domain);
        $apiUrl      = "vue-dashboard-staging.madstreetden.com/api/v1/events/";
        $client      = $this->httpClientFactory->create();
        $client->post($apiUrl, $data_string);
        $client->getBody();
        return $this;
    }
}
