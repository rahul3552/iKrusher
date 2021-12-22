<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Form;

/**
 * Class Checkmo for adding check number.
 */
class Checkmo extends \Magento\OfflinePayments\Block\Form\Checkmo
{

    /**
     * Checkmo template
     *
     * @var string
     */
    protected $_template = 'I95DevConnect_MessageQueue::form/checkmo.phtml';
}
