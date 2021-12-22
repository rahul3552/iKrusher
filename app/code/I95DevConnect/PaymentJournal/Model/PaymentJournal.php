<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Model;

/**
 * Model class for PaymentJournal
 */
class PaymentJournal extends \Magento\Framework\Model\AbstractModel
{
    
    public function _construct()
    {
        $this->_init("I95DevConnect\PaymentJournal\Model\ResourceModel\PaymentJournal");
    }
}
