<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Helper;

/**
 * Class to send service request
 */
class ServiceRequest extends \Magento\Framework\App\Helper\AbstractHelper
{

    private $curl;
    private $storeManager;
    private $logger;
    protected $enc;

    /**
     * ServiceRequest constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Encryption\EncryptorInterface $enc
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $enc
    ) {
        $this->curl = $curl;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->enc = $enc;
        parent::__construct($context);
    }

    /**
     * Curl request
     * @param string $methodtype
     * @param string $curlurl
     * @param string $string
     * @param string $url_param
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function curlRequest($methodtype, $curlurl, $string = null, $url_param = null)
    {
        try {
            $isI95DevRestReq = (isset($url_param['isI95DevRestReq'])) ? $url_param['isI95DevRestReq'] : 0;

            $putData = (isset($url_param['putData'])) ? $url_param['putData'] . "/" : null;
            $this->logger->createLog(
                __METHOD__,
                "===Magento API End Point===" . $curlurl,
                \I95DevConnect\MessageQueue\Api\LoggerInterface::MSGLOGNAME,
                'info'
            );
            if ($string != null) {
                $this->logger->createLog(
                    __METHOD__,
                    "===Request String to Magento API===" . json_encode($string),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::MSGLOGNAME,
                    'info'
                );
            }
            $url = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_DIRECT_LINK,
                true
            );
            $url .='index.php/rest/';
            $token = $this->getToken();
            $this->curl->setHeaders(["Content-Type" => "application/json",
                "Authorization" => "Bearer $token"]);
            if ($methodtype == 'GET') {
                $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'GET');
                $serviceUrl = $url . $curlurl . '/?isI95DevRestReq='.$isI95DevRestReq;
                $this->curl->get($serviceUrl);
            } elseif ($methodtype == 'POST') {
                $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'POST');
                $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($string));
                $serviceUrl = $url . $curlurl . '/?isI95DevRestReq='.$isI95DevRestReq;
                $this->curl->post($serviceUrl, $string);
            } elseif ($methodtype == 'PUT') {
                $serviceUrl = $url . $curlurl  . $putData;
                if (!empty($isI95DevRestReq)) {
                    $serviceUrl .= '?isI95DevRestReq='.$isI95DevRestReq;
                }

                $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
                $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($string));
                $this->curl->post($serviceUrl, $string);
            }
        } catch (\Exception $ex) {
            $this->logger->createLog(
                '__METHOD__',
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        $result = json_decode($this->curl->getBody(), 1);
        $this->checkServiceResponse($result);
        return $result;
    }

    /**
     * To get token
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToken()
    {
        try {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
            $token = $this->scopeConfig->getValue(
                'i95dev_messagequeue/I95DevConnect_credentials/token',
                $storeScope,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        if (!empty($token) && !is_object($token)) {
            return $token;
        } else {
            return false;
        }
    }

    /**
     * Will check service response
     * @param array $result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkServiceResponse($result)
    {
        if (isset($result['message'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result['message']));
        }
    }
}
