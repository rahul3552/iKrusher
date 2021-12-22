<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Api\Data;

/**
 * Represents Data Object for a ERP MessageQueue Data
 */
interface I95DevErpMQInterface //NOSONAR
{
    const MSG_ID = 'msg_id';
    const ERP_CODE = 'erp_code';
    const ENTITY_CODE = 'entity_code';
    const CREATED_DT = 'created_dt';
    const UPDATED_DT = 'updated_dt';
    const STATUS = 'status';
    const MAGENTO_ID = 'magento_id';
    const TARGET_ID = 'target_id';
    const ERROR_ID = 'error_id';
    const COUNTER = 'counter';
    const REF_NAME = 'ref_name';
    const IS_DATA_ERROR = 'is_data_error';
    const DESTINATION_MSG_ID = 'destination_msg_id';
    const ADDITIONAL_INFO = 'additional_info';

    /**
     * get Message Queue Id
     *
     * @return int|null
     */
    public function getMsgId();

    /**
     * Set Message Queue Id
     *
     * @param int $msgId
     * @return $this
     */
    public function setMsgId($msgId);

    /**
     * get Entity code
     *
     * @return string|null
     */
    public function getEntityCode();

    /**
     * set entity code
     *
     * @param string $entityCode
     * @return $this
     */
    public function setEntityCode($entityCode);

    /**
     * get created date
     *
     * @return string|null
     */
    public function getCreatedDt();

    /**
     * set created date
     *
     * @param string $createdDt
     * @return $this
     */
    public function setCreatedDt($createdDt);

    /**
     * get updated date
     *
     * @return string|null
     */
    public function getUpdatedDt();

    /**
     * set updated date
     *
     * @param string $updatedDt
     * @return $this
     */
    public function setUpdatedDt($updatedDt);

    /**
     * get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * get target id
     *
     * @return int|null
     */
    public function getTargetId();

    /**
     * set target id
     *
     * @param int $targetId
     * @return $this
     */
    public function setTargetId($targetId);

    /**
     * get error id
     *
     * @return int|null
     */
    public function getErrorId();

    /**
     * set error id
     *
     * @param int $errorId
     * @return $this
     */
    public function setErrorId($errorId);

    /**
     * get counter
     *
     * @return int|null
     */
    public function getCounter();

    /**
     * set counter
     *
     * @param int $counter
     * @return $this
     */
    public function setCounter($counter);

    /**
     * get reference name
     *
     * @return string|null
     */
    public function getRefName();

    /**
     * set reference name
     *
     * @param string $refName
     * @return $this
     */
    public function setRefName($refName);

    /**
     * get if there is error in data
     *
     * @return int|null
     */
    public function getIsDataError();

    /**
     * set if there is error in data
     *
     * @param int $isDataError
     * @return $this
     */
    public function setIsDataError($isDataError);

    /**
     * get data id
     *
     * @return null|int
     */
    public function getDataId();

    /**
     * get data id
     *
     * @param null|string $dataId
     * @return $this
     */
    public function setDataId($dataId);

    /**
     * get data string
     *
     * @return null|string
     */
    public function getDataString();

    /**
     * get data string
     *
     * @param null|string $dataString
     * @return $this
     */
    public function setDataString($dataString);
    
    /**
     * get destination msg id
     *
     * @return null|string
     */
    public function getDestinationMsgId();

    /**
     * get destination msg id
     *
     * @param null|string $destinationMsgId
     * @return $this
     */
    public function setDestinationMsgId($destinationMsgId);
    
    /**
     * get Erp Code
     *
     * @return string
     */
    public function getErpCode();

    /**
     * set Erp Code
     *
     * @param string $erpCode
     * @return $this
     */
    public function setErpCode($erpCode);
    
    /**
     * get magento id
     *
     * @return string|null
     */
    public function getMagentoId();

    /**
     * set magento id
     *
     * @param string $magentoId
     * @return $this
     */
    public function setMagentoId($magentoId);

    /**
     * get additional info
     *
     * @return null|string
     */
    public function getAdditionalInfo();

    /**
     * set additional info
     *
     * @param null|string $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);
}
