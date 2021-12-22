<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model\ServiceMethod;

/**
 * Class for Magento to ERP sync
 */
class ForwardSync extends AbstractServiceMethod
{
    const STATUS = "status";
    const ERROR = "Error";
    const RES_DATA = "responseData";
    const REQ_DATA = "requestData";
    const IN_DATA = "inputData";

    public $sendIds;
    public $sendEntityData;
    public $sendEntityResponse;

    /**
     * Constructor for DI
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \I95DevConnect\MessageQueue\Model\I95DevResponse $i95DevResponse
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendIds $sendIds
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendEntityData $sendEntityData
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendEntityResponse $sendEntityResponse
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\Logger $logger,
        \I95DevConnect\MessageQueue\Model\I95DevResponse $i95DevResponse,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendIds $sendIds,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendEntityData $sendEntityData,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendEntityResponse $sendEntityResponse
    ) {

        $this->sendIds = $sendIds;
        $this->sendEntityData = $sendEntityData;
        $this->sendEntityResponse = $sendEntityResponse;

        parent::__construct($logger, $i95DevResponse, $scopeConfigInterface, $storeManager);
    }

    /**
     * Method to get collection from Magento and send to ERP
     *
     * @param $entityCode
     * @param $dataString
     * @param string|null $erpName
     * @return Object
     * @throws \Exception
     */
    public function sendEntityData($entityCode, $dataString, $erpName = null)
    {
        try {
            $finalRequest = $this->convertInputStringToArray($dataString);
            $data = $this->sendIds($entityCode, $finalRequest);

            if (!empty($data)) {
                $responseData = $this->sendEntityData->getEntityData($entityCode, $data, $erpName);

                if (isset($responseData[self::STATUS])) {
                    $this->setResponse(
                        $responseData[self::STATUS],
                        $responseData['message'],
                        $responseData[self::RES_DATA]
                    );
                }
            } else {
                $this->setResponse("false", "No data Exist for sync", null);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }

        return $this->i95DevResponse;
    }

    /**
     * send response to ERP
     * @param string $entityCode
     * @param string $dataString
     * @param string $erpName
     * @return \I95DevConnect\MessageQueue\Model\I95DevResponse $i95Devresponse
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function sendEntityResponse($entityCode, $dataString, $erpName = null)
    {
        try {
            $finalRequest = $this->convertInputStringToArray($dataString);

            if (isset($finalRequest[self::REQ_DATA])) {
                foreach ($finalRequest[self::REQ_DATA] as $key => $requestData) {
                    if (isset($requestData[self::IN_DATA])) {
                        $finalRequest[self::REQ_DATA][$key][self::IN_DATA] = $this->convertInputStringToArray(
                            $this->decryptDES($requestData[self::IN_DATA])
                        );
                    }
                }
            }

            if (isset($finalRequest[self::REQ_DATA])) {
                $responseData = $this->sendEntityResponse->getEntityResponse(
                    $entityCode,
                    $finalRequest[self::REQ_DATA],
                    $erpName
                );

                $this->processResponseData($responseData);

            } else {
                $this->setResponse("false", "No data exists in response data", null);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }

        return $this->i95DevResponse;
    }

    /**
     * @param $responseData
     */
    public function processResponseData($responseData)
    {
        if (isset($responseData[self::STATUS])) {
            foreach ($responseData[self::RES_DATA] as $key => $requestData) {
                if (isset($requestData[self::IN_DATA])) {
                    $preparedata['addressess'] = $requestData[self::IN_DATA];
                    $inputData = $this->encryptAES(json_encode($preparedata));
                    $responseData[self::RES_DATA][$key][self::IN_DATA] = $inputData;
                }
            }

            $this->setResponse(
                $responseData[self::STATUS],
                $responseData['message'],
                $responseData[self::RES_DATA]
            );
        }
    }

    /**
     * send id list available in outbound MQ
     * @param string $entityCode
     * @param null $requestData
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function sendIds($entityCode, $requestData = null)
    {
        try {
            $updatedIdList = $this->sendIds->defaultUpdatedEntityIds($entityCode, $requestData);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }

        return $updatedIdList;
    }
}
