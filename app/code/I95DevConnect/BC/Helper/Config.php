<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_QuickBooks
 */

namespace I95DevConnect\BC\Helper;

use Magento\Framework\Exception\LocalizedException;

/**
 * Helper Class returns the configuration of connector
 */
class Config extends \I95DevConnect\MessageQueue\Helper\Config
{

    /**
     * Will return the component from connector configurations
     *
     * @return Varien_Object
     */
    public function getConfigValues()
    {
        try {
            $this->obj->setData('component', 'BC');
        } catch (LocalizedException $ex) {
            $this->data->createLog(__METHOD__, $ex->getMessage(), "i95devException", 'critical');
        }
        return $this->obj;
    }
}
