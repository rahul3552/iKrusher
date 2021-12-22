<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_Smtp
 */


namespace Aitoc\Smtp\Plugin\Framework\Mail;

use Aitoc\Smtp\Model\Html2Text;
use Magento\Framework\Mail\Message as MailMessage;

class Message
{
    /**
     * @var \Aitoc\Smtp\Model\Config
     */
    private $config;

    public function __construct(
        \Aitoc\Smtp\Model\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param MailMessage $subject
     * @return MailMessage
     */
    public function afterSetBody(MailMessage $subject)
    {
        if (!$this->config->plainEnabled()) {
            return $subject;
        }

        try {
            $body = $subject->getBody();
            if ($body instanceof \Zend\Mime\Message && !$body->isMultiPart()) {
                $reflection = new \ReflectionProperty(MailMessage::class, 'zendMessage');
                $reflection->setAccessible(true);
                /** @var \Zend\Mail\Message $zendMessage */
                $zendMessage = $reflection->getValue($subject);

                $plainContent = '';
                try {
                    $plainContent = Html2Text::convert($zendMessage->getBodyText());
                } catch (\Exception $e) {
                }

                $textPart = new \Zend\Mime\Part($plainContent);
                $textPart->setCharset($zendMessage->getEncoding());
                $textPart->setType(\Zend\Mime\Mime::TYPE_TEXT);
                $body->setParts(array_merge([$textPart], $body->getParts()));
                $zendMessage->setBody($body);
                $zendMessage->getHeaders()->get('content-type')->setType('multipart/alternative');
            }
        } catch (\Exception $e) {
        }

        return $subject;
    }
}
