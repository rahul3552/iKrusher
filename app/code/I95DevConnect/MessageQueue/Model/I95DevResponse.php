<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use \I95DevConnect\MessageQueue\Api\I95DevResponseInterface;

/**
 * Data object for response
 */
class I95DevResponse implements I95DevResponseInterface
{
    public $status;
    public $resultData;
    public $message;
    public $result;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getResultdata()
    {
        return $this->resultData;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function setResultdata($resultData)
    {
        $this->resultData = $resultData;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
