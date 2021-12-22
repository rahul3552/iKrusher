<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Api\Data;

/**
 * Represents Data Object for a Magento MessageQueue Data
 */
interface I95DevMagentoMQInterface //NOSONAR
{
    const MSG_ID = 'msg_id';
    const ERP_CODE = 'erp_code';
    const ENTITY_CODE = 'entity_code';
    const CREATED_DT = 'created_dt';
    const UPDATED_DT = 'updated_dt';
    const STATUS = 'status';
    const MAGENTO_ID = 'magento_id';
    const TARGET_ID = 'target_id';
    const UPDATED_BY = 'updated_by';
    const ERROR_ID = 'error_id';
    const DESTINATION_UPADTED_DATE = 'destination_updated_dt';
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
     * get Erp Code
     *
     * @return string
     */
    public function getErpCode();

    /**
     * get Erp Code
     *
     * @param string $erpCode
     * @return $this
     */
    public function setErpCode($erpCode);

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
     * get UpdatedBy
     *
     * @return int|null
     */
    public function getUpdatedBy();

    /**
     * set UpdatedBy
     *
     * @param int $updatedBy
     * @return $this
     */
    public function setUpdatedBy($updatedBy);

    /**
     * get reference name
     *
     * @return string|null
     */
    public function getErrorId();

    /**
     * set reference name
     *
     * @param string $errorId
     * @return $this
     */
    public function setErrorId($errorId);

    /**
     * get data string
     *
     * @return null|string
     */
    public function getDestinationUpdatedDate();

    /**
     * get data string
     *
     * @param null|string $destinationUpdatedDate
     * @return $this
     */
    public function setDestinationUpdatedDate($destinationUpdatedDate);
    
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
}
