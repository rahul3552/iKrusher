<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ConfigValues;

/**
 * Class for Getting Message Queue Packet Size
 */
class PacketSize implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Getting Message Queue Packet Size
     *
     * @return array
     */
    public function toOptionArray()
    {
        $value = 'value';
        $label = 'label';
        return [
            [$value => 50, $label => __(50)],
            [$value => 100, $label => __(100)],
            [$value => 500, $label => __(500)]
        ];
    }
}
