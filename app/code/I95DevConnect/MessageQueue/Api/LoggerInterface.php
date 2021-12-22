<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Api;

/**
 * Interface for i95dev Log creation.
 */
interface LoggerInterface
{

    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';
    const GENERIC = 'Generic';
    const MSGLOGNAME = 'MessageToMagento';
    const CRITICAL = 'critical';
    const INFO = 'info';

    /**
     *
     * @param string $logArea
     * @param string $message
     * @param string $logName
     * @param string $logType
     * @return void
     */
    public function createLog($logArea, $message, $logName, $logType);
}
