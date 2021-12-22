<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use I95DevConnect\MessageQueue\Api\I95DevReverseResponseInterface;

/**
 * I95DevReverseResponse model
 */
class I95DevReverseResponse implements I95DevReverseResponseInterface
{

    public $result;
    public $messageId;
    public $message;
    public $targetId;
    public $inputData;
    public $targetcustomerid;

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
    public function getMessageid()
    {
        return $this->messageId;
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
     *
     * {@inheritdoc}
     */
    public function getTargetid()
    {
        return $this->targetId;
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
    public function setMessageid($messageId)
    {
        $this->messageId = $messageId;
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

    /**
     *
     * {@inheritdoc}
     */
    public function setTargetid($targetId)
    {
        $this->targetId = $targetId;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getInputdata()
    {
        return $this->inputData;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function setInputdata($inputData)
    {
        $this->inputData = $inputData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetcustomerid()
    {
        return $this->targetcustomerid;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function setTargetcustomerid($targetcustomerid)
    {
        $this->targetcustomerid = $targetcustomerid;
        return $this;
    }
}
