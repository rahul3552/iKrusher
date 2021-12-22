<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

use I95DevConnect\CloudConnect\Api\Data\ResponseInterfaceFactory;
use I95DevConnect\CloudConnect\Helper\Data;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Model\AbstractModel;
use \I95DevConnect\CloudConnect\Model\Logger;
use \I95DevConnect\CloudConnect\Model\Request;

/**
 * Class for service request
 */
class Service extends AbstractModel
{

    /**
     * scopeConfig for system Congiguration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     * @var Data
     */
    public $cloudHelper;
    public $curl;
    public $responseInterface;
    public $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;
    public $devResponse;
    /**
     * @var \I95DevConnect\CloudConnect\Model\Request
     */
    public $request;
    /**
     * @var string
     */
    public $serviceEntityCode;

    /**
     * Constructor for DI
     * @param Data $cloudHelper
     * @param Curl $curl
     * @param ResponseInterfaceFactory $responseInterface
     * @param LoggerFactory $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \I95DevConnect\CloudConnect\Model\Request $request
     * @param string $serviceEntityCode
     */
    public function __construct(
        Data $cloudHelper,
        Curl $curl,
        ResponseInterfaceFactory $responseInterface,
        LoggerFactory $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Request $request,
        $serviceEntityCode
    ) {
        $this->cloudHelper = $cloudHelper;
        $this->curl = $curl;
        $this->responseInterface = $responseInterface;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->request = $request;
        $this->serviceEntityCode = $serviceEntityCode;
    }

    /**
     * make call to get the schedular id
     * @param string $schedulerType
     * @param string $entity
     * @param Object $requestData
     * @param int $schedulerId
     * @param string $otherUrl
     * @return Object
     * @noinspection PhpDocSignatureInspection
     */
    public function makeServiceCall(
        $schedulerType,
        $entity = null,
        $requestData = null,
        $schedulerId = 0,
        $otherUrl = ''
    ) {
        try {
            if (empty($requestData)) {
                $requestParam = $this->request->cloudRequest($schedulerType, $entity, $schedulerId);
            } else {
                $requestParam = $requestData;
            }

            return $this->_doRestCall($requestParam, $schedulerType, $entity, $otherUrl, $schedulerId);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PullDataCron',
                $ex->getMessage(),
                \I95DevConnect\CloudConnect\Model\Logger::EXCEPTION,
                \I95DevConnect\CloudConnect\Model\Logger::CRITICAL
            );

            return null;
        }
    }

    /**
     * Soap call
     * @param Object $transport
     * @param $schedulerType
     * @param $entity
     * @param $otherUrl
     * @param int $schedulerId
     * @return Object
     */
    public function _doRestCall($transport, $schedulerType, $entity = null, $otherUrl = '', $schedulerId = 0)
    {
        $finalResponse = $this->jsonHelper->jsonEncode(json_decode("{}"));

        try {
            $targetUrl = $this->cloudHelper->getTargetUrl();
            if (!$this->cloudHelper->isEnabled()) {
                $this->setDevResponse(
                    "",
                    "Please Enable i95Dev Generic Connector",
                    $finalResponse,
                    true,
                    false,
                    $schedulerId
                );
                return $this->devResponse;
            }
            $logAs = 'info';

            if (!empty($entity)) {
                if (isset($this->serviceEntityCode[$entity])) {
                    $entity = $this->serviceEntityCode[$entity];
                }
                $finalUrl = $targetUrl . '/' . $entity . '/' . $schedulerType;
            } else {
                $finalUrl = $targetUrl . '/Index';
                $logAs = 'debug';
            }

            if ($otherUrl != '') {
                $finalUrl = $targetUrl . '/Mapping/' . $otherUrl;
            }

            try {
                //To encrypt transport data
                $encryptObj = $this->jsonHelper->jsonEncode($transport);
                $logType = $this->logger->create()->getEntityLogType($schedulerType, $entity);

                $refreshToken = $this->cloudHelper->getApiAuthenticationToken();
                $accessToken = $this->getAccessToken($refreshToken);
                $response = $this->curlCall($accessToken, $encryptObj, $finalUrl);

                $this->logger->create()->createLog(
                    "Request to: " . $finalUrl.PHP_EOL.
                    "Request data to cloud: " . $encryptObj,
                    "Response data from cloud: " . $response,
                    $logType,
                    $logAs
                );

                $responseObject = !empty($response) ? json_decode($response) : "";
                if (is_object($responseObject)) {
                    $this->processResponseObject($responseObject);
                } else {
                    $this->setDevResponse("", "empty response received", false, true, false, $schedulerId);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $msg = $e->getMessage();
                $errorMessage = str_replace("Server was unable to process request. --->", "", $msg);
                $this->logger->create()->createLog(
                    __METHOD__,
                    $errorMessage,
                    Logger::EXCEPTION,
                    'critical'
                );
                $this->setDevResponse("", $errorMessage, $finalResponse, true, false, $schedulerId);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $msg = $ex->getMessage();
            $this->logger->create()->createLog(
                __METHOD__,
                $msg,
                Logger::EXCEPTION,
                'critical'
            );
            $this->setDevResponse("", $msg, $finalResponse, true, false, $schedulerId);
        }
        return $this->devResponse;
    }

    /**
     * @param $responseObject
     */
    public function processResponseObject($responseObject)
    {
        $isConfiguration = [
            'entity' => (isset($responseObject->isConfigurationUpdated)) ?
                $responseObject->isConfigurationUpdated : false,
            'payment' => (isset($responseObject->isPaymentMappingUpdated)) ?
                $responseObject->isPaymentMappingUpdated : false,
            'shipping' => (isset($responseObject->isShippingMappingUpdated)) ?
                $responseObject->isShippingMappingUpdated : false
        ];

        $resultant = isset($responseObject->resultData)?$responseObject->resultData:null;
        $subscriptionActive = isset($responseObject->isSubscriptionActive)?$responseObject->isSubscriptionActive:null;
        $this->setDevResponse(
            $isConfiguration,
            (isset($responseObject->message))?$responseObject->message:null,
            $resultant,
            $subscriptionActive,
            $responseObject->result,
            (isset($responseObject->schedulerId)) ? $responseObject->schedulerId : false
        );
    }

    /**
     * Method to set response
     * @param bool $isConfiguration
     * @param string $msg
     * @param string $resultData
     * @param bool $isActive
     * @param bool $result
     * @param string $schedularId
     */
    public function setDevResponse($isConfiguration, $msg, $resultData, $isActive, $result, $schedularId)
    {
        $this->devResponse = $this->responseInterface->create();
        if (is_array($isConfiguration)) {
            $this->devResponse->setIsConfigurationUpdated($isConfiguration['entity']);
            $this->devResponse->setIsShippingMappingUpdated($isConfiguration['shipping']);
            $this->devResponse->setIsPaymentMappingUpdated($isConfiguration['payment']);
        }

        $this->devResponse->setMessage($msg);
        $this->devResponse->setResultData($resultData);
        $this->devResponse->setIsSubscriptionActive($isActive);
        $this->devResponse->setResult($result);
        $this->devResponse->setSchedulerId($schedularId);
    }

    /**
     * @param $token
     * @param $encryptObj
     * @param $finalUrl
     * @return mixed
     */
    public function curlCall($token, $encryptObj, $finalUrl)
    {
        $authorization = "Bearer $token";
        $this->curl->setHeaders(['Content-Type' => 'application/json',
            'Content-Length' => strlen($encryptObj),
            'Authorization' => $authorization]);
        $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->curl->setOption(CURLOPT_POSTFIELDS, $encryptObj);
        $this->curl->post($finalUrl, $encryptObj);
        return $this->curl->getBody();
    }

    /**
     * @param $refreshToken
     * @return bool
     */
    public function getAccessToken($refreshToken)
    {
        try {
            $finalUrl = $this->cloudHelper->getTargetUrl() . "/Client/Token";
            $encryptObj = $this->jsonHelper->jsonEncode(["refreshToken" => $refreshToken]);
            $this->curl->setHeaders(['Content-Type' => 'application/json',
            'Content-Length' => strlen($encryptObj)]);
            $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'POST');
            $this->curl->setOption(CURLOPT_POSTFIELDS, $encryptObj);
            $this->curl->post($finalUrl, $encryptObj);
            $response = $this->curl->getBody();

            $this->logger->create()->createLog(
                "Request data to cloud: " . $encryptObj,
                "Response data from cloud: " . $response,
                "AccessToken",
                'debug'
            );
            $responseObject = !empty($response) ? $this->jsonHelper->jsonDecode($response) : "";
            if (is_array($responseObject)) {
                return (isset($responseObject['accessToken'])) ? $responseObject['accessToken']['token'] : false;
            } else {
                return false;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->create()->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }
    }
}
