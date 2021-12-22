<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace I95DevConnect\MessageQueue\Model\Config\Source;

class Logtype implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [['value' => 'info', 'label' => __('info')],['value' => 'critical', 'label' => __('critical')],['value' => 'error', 'label' => __('error')],['value' => 'debug', 'label' => __('debug')]];
    }

    public function toArray()
    {
        return ['info' => __('info'),'critical' => __('critical'),'error' => __('error'),'debug' => __('debug')];
    }
}

