<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Block\Info;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Block\Info;

/**
 * Class for credit limit payment
 */
class Creditlimits extends Info
{

    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_NetTerms::info/creditlimit.phtml';

    /**
     * Get payment title
     * @return string
     * @throws LocalizedException
     */
    public function getTitle()
    {
        $paymentData = $this->getMethod()->getInfoInstance()->getData();
        $requestParameters = $paymentData['additional_information'];
        return (isset($requestParameters['method_title']) ? $requestParameters['method_title'] :$this->getMethod()
            ->getTitle());
    }
}
