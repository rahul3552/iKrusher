<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Mail;

use Magento\Framework\Mail\TransportInterfaceFactory;
use Mageplaza\CustomForm\Model\MailEvent;

/**
 * Class TransportFactory
 * @package Mageplaza\CustomForm\Mail
 */
class TransportFactory
{
    /**
     * @var MailEvent
     */
    private $mailEvent;

    /**
     * TransportFactory constructor.
     *
     * @param MailEvent $mailEvent
     */
    public function __construct(
        MailEvent $mailEvent
    ) {
        $this->mailEvent = $mailEvent;
    }

    /**
     * @param TransportInterfaceFactory $subject
     * @param array $data
     *
     * @return array
     */
    public function beforeCreate(TransportInterfaceFactory $subject, array $data = [])
    {
        if (isset($data['message'])) {
            $this->mailEvent->dispatch($data['message']);
        }

        return [$data];
    }
}
