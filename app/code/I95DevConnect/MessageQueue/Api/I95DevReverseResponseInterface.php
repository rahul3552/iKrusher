<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Api;

/**
 * Reverse Response Interface.
 */
interface I95DevReverseResponseInterface
{

    /**
     * get status
     * @return bool
     */
    public function getResult();

    /**
     * get message Id
     * @return string
     */
    public function getMessageid();

    /**
     * get message
     * @return string
     */
    public function getMessage();

    /**
     * get message
     * @return string
     */
    public function getTargetid();

    /**
     * get input data
     * @return string
     */
    public function getInputdata();

    /**
     * set result
     * @param bool $result
     * @return $this
     */
    public function setResult($result);

    /**
     * set message id
     * @param string $messageId
     * @return $this
     */
    public function setMessageid($messageId);

    /**
     * set message
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * set target id
     * @param string $targetId
     * @return $this
     */
    public function setTargetid($targetId);

    /**
     * set input data
     * @param [] $inputData
     * @return $this
     */
    public function setInputdata($inputData);

    /**
     * set target customer id
     * @param string $targetcustomerid
     * @return $this
     */
    public function setTargetcustomerid($targetcustomerid);
}
