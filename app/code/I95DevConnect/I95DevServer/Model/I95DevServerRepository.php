<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model;

use \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterface;
use Magento\Tests\NamingConvention\true\mixed;

/**
 * Model class for implementing I95DevServerRepositoryInterface
 */
class I95DevServerRepository implements I95DevServerRepositoryInterface
{
    const RESPONSE = "===Response===";
    const ERROR_STATUS = "Error";

    public $erpName = "ERP";
    public $logger;
    public $helperData;
    public $scopeConfig;
    public $i95DevResponse;
    public $reverseSync;
    public $currentMethodProperties;
    public $forwardSync;
    public $apiServiceMethodRoutes;
    public $currententitiyCode;

    /**
     * Constructor for DI
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param \I95DevConnect\MessageQueue\Helper\Data $helperData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \I95DevConnect\MessageQueue\Model\I95DevResponse $i95DevResponse
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ReverseSync $reverseSync
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync $forwardSync
     * @param  mixed $apiServiceMethodRoutes
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \I95DevConnect\MessageQueue\Helper\Data $helperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \I95DevConnect\MessageQueue\Model\I95DevResponse $i95DevResponse,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ReverseSync $reverseSync,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync $forwardSync,
        $apiServiceMethodRoutes
    ) {

        $this->logger = $logger;
        $this->helperData = $helperData;
        $this->scopeConfig = $scopeConfig;
        $this->i95DevResponse = $i95DevResponse;
        $this->reverseSync = $reverseSync;
        $this->apiServiceMethodRoutes = $apiServiceMethodRoutes;
        $this->forwardSync = $forwardSync;
    }

    /**
     * Method to process inbound records to Magento
     */
    public function syncMQtoMagento()
    {
        $this->reverseSync->syncMQtoMagento();
    }

    /**
     * {@inheritDoc}
     */
    public function serviceMethod($methodName, $inputString = null, $erpName = null)
    {
        if ($this->helperData->isEnabled()) {
            $this->erpName = ($erpName)? $erpName : __($this->helperData->getComponent());

            $this->logger->createLog(__METHOD__, "===Request===" . $inputString, $methodName, 'info');
            try {
                $response = [];

                if ($this->checkMethodExistsInService($methodName)) {
                    $methodType = $this->currentMethodProperties['methodType'];
                    $this->currententitiyCode = $this->currentMethodProperties['entityCode'];
                    if ($methodType == 'reverse') {
                        $response = $this->reverseSync->syncDataToMQ(
                            $methodName,
                            $inputString,
                            $this->currentMethodProperties,
                            $this->erpName
                        );
                    } else {
                        $response = $this->routeForwardEntity($inputString, $methodName);
                    }
                } else {
                    $this->logger->createLog(
                        __METHOD__,
                        self::RESPONSE . json_encode($this->i95DevResponse),
                        $methodName,
                        'info'
                    );
                    $response = $this->i95DevResponse;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->logger->createLog(
                    __METHOD__,
                    $e->getMessage(),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'critical'
                );
            }
            $this->logger->createLog(__METHOD__, self::RESPONSE . json_encode($response), $methodName, 'info');
            return $response;
        } else {
            $this->i95DevResponse->setStatus(self::ERROR_STATUS);
            $this->i95DevResponse->setMessage("I95Dev Connector Extension is disabled");
            $this->logger->createLog(
                __METHOD__,
                self::RESPONSE . json_encode($this->i95DevResponse),
                $methodName,
                'info'
            );
            return $this->i95DevResponse;
        }
    }

    /**
     * @param $inputString
     * @param $methodName
     * @return \I95DevConnect\MessageQueue\Model\I95DevResponse|Object
     * @throws \Exception
     */
    public function routeForwardEntity($inputString, $methodName)
    {
        if (isset($this->currentMethodProperties['sendInfo'])) {
            $response = $this->forwardSync->sendEntityData(
                $this->currententitiyCode,
                $inputString,
                $this->erpName
            );
        } elseif (isset($this->currentMethodProperties['setResponse'])) {
            $response = $this->forwardSync->sendEntityResponse(
                $this->currentMethodProperties['entityCode'],
                $inputString,
                $this->erpName
            );
        } elseif (isset($this->currentMethodProperties['setMQResponse'])) {
            $response = $this->reverseSync->getMessageQueueStatus($inputString);
        } elseif (isset($this->currentMethodProperties['setMQResponseAck'])) {
            $response = $this->reverseSync->setMessageQueueAck($inputString);
        } else {
            $this->i95DevResponse->setStatus(self::ERROR_STATUS);
            $this->i95DevResponse->setMessage('Method Not exists');
            $this->logger->createLog(
                __METHOD__,
                self::RESPONSE . json_encode($this->i95DevResponse),
                $methodName,
                'info'
            );
            $response = $this->i95DevResponse;
        }

        return $response;
    }
    /**
     *
     * @param string $methodName
     * @return bool
     */
    private function checkMethodExistsInService($methodName)
    {
        if (isset($this->apiServiceMethodRoutes[$methodName])) {
            $this->currentMethodProperties = $this->apiServiceMethodRoutes[$methodName];
            return true;
        }

        $this->i95DevResponse->setStatus(self::ERROR_STATUS);
        $this->i95DevResponse->setMessage('Method Not exists');
        return false;
    }
}
