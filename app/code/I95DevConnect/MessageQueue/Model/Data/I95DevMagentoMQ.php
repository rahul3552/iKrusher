<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */
namespace I95DevConnect\MessageQueue\Model\Data;

use \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterface;

/**
 * I95Dev Magento MessageQueue Model
 */
class I95DevMagentoMQ extends \Magento\Framework\Model\AbstractModel implements I95DevMagentoMQInterface //NOSONAR
{

    public $dataString;
    public $dataId;

    /**
     * get Message Queue Id
     *
     * @return int|null
     */
    public function getMsgId()
    {
        return $this->getData(self::MSG_ID);
    }

    /**
     * Set Message Queue Id
     *
     * @param int $msgId
     *
     * @return void
     */
    public function setMsgId($msgId)
    {
        $this->setData(self::MSG_ID, $msgId);
    }

    /**
     * get Erp Code
     *
     * @return string
     */
    public function getErpCode()
    {

        return $this->getData(self::ERP_CODE);
    }

    /**
     * get Erp Code
     *
     * @param string $erpCode
     * @return void
     */
    public function setErpCode($erpCode)
    {
        $this->setData(self::ERP_CODE, $erpCode);
    }

    /**
     * get Entity code
     *
     * @return string|null
     */
    public function getEntityCode()
    {
        return $this->getData(self::ENTITY_CODE);
    }

    /**
     * set entity code
     *
     * @param $ENTITY_CODE
     * @return void
     */
    public function setEntityCode($ENTITY_CODE)
    {
        $this->setData(self::ENTITY_CODE, $ENTITY_CODE);
    }

    /**
     * get created date
     *
     * @return string|null
     */
    public function getCreatedDt()
    {
        return $this->getData(self::CREATED_DT);
    }

    /**
     * set created date
     *
     * @param string $createdDt
     * @return void
     */
    public function setCreatedDt($createdDt)
    {
        $this->setData(self::CREATED_DT, $createdDt);
    }

    /**
     * get updated date
     *
     * @return string|null
     */
    public function getUpdatedDt()
    {
        return $this->getData(self::UPDATED_DT);
    }

    /**
     * set updated date
     *
     * @param string $updatedDt
     * @return void
     */
    public function setUpdatedDt($updatedDt)
    {
        $this->setData(self::UPDATED_DT, $updatedDt);
    }

    /**
     * get status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * set status
     *
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * get magento Id
     *
     * @return int|null
     */
    public function getMagentoId()
    {
        return $this->getData(self::MAGENTO_ID);
    }

    /**
     * set Magento Id
     *
     * @param int $magentoId
     * @return void
     */
    public function setMagentoId($magentoId)
    {
        $this->setData(self::MAGENTO_ID, $magentoId);
    }

    /**
     * get target id
     *
     * @return void
     */
    public function getTargetId()
    {
        $this->getData(self::TARGET_ID);
    }

    /**
     * set target id
     *
     * @param int $targetId
     * @return void
     */
    public function setTargetId($targetId)
    {
        $this->setData(self::TARGET_ID, $targetId);
    }

    /**
     * get Updated By
     *
     * @return int|null
     */
    public function getUpdatedBy()
    {
        return $this->getData(self::UPDATED_BY);
    }

    /**
     * set Updated By
     *
     * @param int $updatedBy
     * @return void
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->setData(self::UPDATED_BY, $updatedBy);
    }

    /**
     * get error id
     *
     * @return int|null
     */
    public function getErrorId()
    {
        return $this->getData(self::ERROR_ID);
    }

    /**
     * set error id
     *
     * @param int $errorId
     * @return void
     */
    public function setErrorId($errorId)
    {
        $this->setData(self::ERROR_ID, $errorId);
    }

    /**
     * get erp msg id
     *
     * @return int|null
     */
    public function getDestinationMsgId()
    {
        return $this->getData(self::DESTINATION_MSG_ID);
    }

    /**
     * set erp msg id
     *
     * @param int $destinationMsgId
     * @return void
     */
    public function setDestinationMsgId($destinationMsgId)
    {
        $this->setData(self::DESTINATION_MSG_ID, $destinationMsgId);
    }

    /**
     * get data string
     *
     * @return null|string
     */
    public function getDestinationUpdatedDate()
    {
        return $this->getData(self::DESTINATION_UPADTED_DATE);
    }

    /**
     * get data string
     *
     * @param null|string $destinationUpdatedDate
     * @return void
     */
    public function setDestinationUpdatedDate($destinationUpdatedDate)
    {
        $this->setData(self::DESTINATION_UPADTED_DATE, $destinationUpdatedDate);
    }
}
