<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */
namespace I95DevConnect\MessageQueue\Model\Data;

use \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterface;

/**
 * I95Dev ERP MessageQueue Model
 */
class I95DevErpMQ extends \Magento\Framework\Model\AbstractModel implements I95DevErpMQInterface //NOSONAR
{

    public $dataString;
    public $dataId;

    /**
     * get Message Queue Id
     *
     * @return void
     */
    public function getMsgId()
    {
        $this->getData(self::MSG_ID);
    }

    /**
     * Set Message Queue Id
     *
     * @param int $msgId
     * @return $this
     */
    public function setMsgId($msgId)
    {
        return $this->setData(self::MSG_ID, $msgId);
    }

    /**
     * get Erp Code
     *
     * @return string
     */
    public function getErpCode()
    {
        $this->getData(self::ERP_CODE);
    }

    /**
     * get Erp Code
     *
     * @param string $erpCode
     * @return $this
     */
    public function setErpCode($erpCode)
    {
        return $this->setData(self::ERP_CODE, $erpCode);
    }

    /**
     * get Entity code
     *
     * @return string|null
     */
    public function getEntityCode()
    {
        $this->getData(self::ENTITY_CODE);
    }

    /**
     * set entity code
     *
     * @param $ENTITY_CODE
     *
     * @return $this
     */
    public function setEntityCode($ENTITY_CODE)
    {
        return $this->setData(self::ENTITY_CODE, $ENTITY_CODE);
    }

    /**
     * get created date
     *
     * @return string|null
     */
    public function getCreatedDt()
    {
        $this->getData(self::CREATED_DT);
    }

    /**
     * set created date
     *
     * @param string $createdDt
     * @return $this
     */
    public function setCreatedDt($createdDt)
    {
        return $this->setData(self::CREATED_DT, $createdDt);
    }

    /**
     * get updated date
     *
     * @return string|null
     */
    public function getUpdatedDt()
    {
        $this->getData(self::UPDATED_DT);
    }

    /**
     * set updated date
     *
     * @param string $updatedDt
     * @return $this
     */
    public function setUpdatedDt($updatedDt)
    {
        return $this->setData(self::UPDATED_DT, $updatedDt);
    }

    /**
     * get status
     *
     * @return string|null
     */
    public function getStatus()
    {
        $this->getData(self::STATUS);
    }

    /**
     * set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * get magento Id
     *
     * @return void
     */
    public function getMagentoId()
    {
        $this->getData(self::MAGENTO_ID);
    }

    /**
     * set Magento Id
     *
     * @param int $magentoId
     * @return $this
     */
    public function setMagentoId($magentoId)
    {
        return $this->setData(self::MAGENTO_ID, $magentoId);
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
     * @return $this
     */
    public function setTargetId($targetId)
    {
        return $this->setData(self::TARGET_ID, $targetId);
    }

    /**
     * get error id
     *
     * @return int|null
     */
    public function getErrorId(): ?int
    {
        $this->getData(self::ERROR_ID);
    }

    /**
     * set error id
     *
     * @param int $errorId
     * @return $this
     */
    public function setErrorId($errorId)
    {
        return $this->setData(self::ERROR_ID, $errorId);
    }

    /**
     * get counter
     *
     * @return int|null
     */
    public function getCounter(): ?int
    {
        $this->getData(self::COUNTER);
    }

    /**
     * set counter
     *
     * @param int $counter
     * @return $this
     */
    public function setCounter($counter)
    {
        return $this->setData(self::COUNTER, $counter);
    }

    /**
     * get reference name
     *
     * @return string|null
     */
    public function getRefName()
    {
        $this->getData(self::REF_NAME);
    }

    /**
     * set reference name
     *
     * @param string $refName
     * @return $this
     */
    public function setRefName($refName)
    {
        return $this->setData(self::REF_NAME, $refName);
    }

    /**
     * get if there is error in data
     *
     * @return int|null
     */
    public function getIsDataError(): ?int
    {
        $this->getData(self::IS_DATA_ERROR);
    }

    /**
     * set if there is error in data
     *
     * @param int $isDataError
     * @return $this
     */
    public function setIsDataError($isDataError)
    {
        return $this->setData(self::IS_DATA_ERROR, $isDataError);
    }

    /**
     * get data id
     *
     * @return null|int
     */
    public function getDataId()
    {
        return $this->dataId;
    }

    /**
     * get data id
     *
     * @param null|string $dataId
     * @return $this
     */
    public function setDataId($dataId)
    {
        $this->dataId =  $dataId;
        return $this->dataId;
    }

    /**
     * get data string
     *
     * @return string|null
     */
    public function getDataString()
    {
        return $this->dataString;
    }

    /**
     * set data string
     *
     * @param string|null $dataString
     * @return $this
     */
    public function setDataString($dataString)
    {
        $this->dataString =  $dataString;
        return $this->dataString;
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
     *
     * @return void
     */
    public function setDestinationMsgId($destinationMsgId)
    {
        $this->setData(self::DESTINATION_MSG_ID, $destinationMsgId);
    }
    
    /**
     * get additional info
     *
     * @return null|string
     */
    public function getAdditionalInfo()
    {
        return $this->getData(self::ADDITIONAL_INFO);
    }

    /**
     * set additional info
     *
     * @param null|string $additionalInfo
     * @return void
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }
}
