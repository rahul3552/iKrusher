<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence;

/**
 * Class which validate ERP data string
 */
class Validate extends \Magento\Framework\Model\AbstractModel
{

    public $validateFields;
    public $dataHelper;

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $registry);
    }

    /**
     *
     * @param type $stringData
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData($stringData)
    {
        $msg = [];

        foreach ($this->validateFields as $key => $value) {
            /*@author Debashis S. Gopal. is_null() and === check added */
            $val = $this->dataHelper->getValueFromArray($key, $stringData);
            if ($val === null || $val === '' || (is_array($val) && count($val) == 0)) {
                $msg[] = __($value);
            }
        }

        if (!empty($msg)) {
            $message = implode(', ', $msg);
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
        return true;
    }
}
