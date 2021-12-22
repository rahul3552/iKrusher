<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Api;

/**
 * Response Interface.
 */
interface I95DevResponseInterface
{

    /**
     * get result
     *
     * @return bool
     */
    public function getResult();

    /**
     * get result data
     * @return I95DevConnect\MessageQueue\Api\I95DevReverseResponseInterface[] | null
     */
    public function getResultdata();

    /**
     * get message
     * @return string
     */
    public function getMessage();

    /**
     * set result
     * @param bool $result
     * @return $this
     */
    public function setResult($result);

    /**
     * set result data
     * @param [] $resultData
     * @return $this
     */
    public function setResultdata($resultData);

    /**
     * set message
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
