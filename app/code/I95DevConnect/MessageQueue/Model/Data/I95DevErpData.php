<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */
namespace I95DevConnect\MessageQueue\Model\Data;

use \I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterface;

/**
 * I95dev ERP Data Model
 */
class I95DevErpData extends \Magento\Framework\Model\AbstractModel implements I95DevErpDataInterface
{

    /**
     * get Data Id
     *
     * @return void
     */
    public function getDataId()
    {
        $this->getData(self::DATA_ID);
    }

    /**
     * Set Data Id
     *
     * @param int $dataId
     * @return $this
     */
    public function setDataId($dataId)
    {
        return $this->setData(self::DATA_ID, $dataId);
    }

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
     * @return $this
     */
    public function setMsgId($msgId)
    {
        return $this->setData(self::MSG_ID, $msgId);
    }

    /**
     * get data string
     *
     * @return string
     */
    public function getDataString()
    {
        $this->getData(self::DATA_STRING);
    }

    /**
     * get data string
     *
     * @param string $dataString
     * @return $this
     */
    public function setDataString($dataString)
    {
        return $this->setData(self::DATA_STRING, $dataString);
    }
}
