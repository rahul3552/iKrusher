<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Test\Unit\Model\Email;

use Aheadworks\Ctq\Model\Email\Sender;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderFactory;

/**
 * Class SenderTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Email
 */
class SenderTest extends TestCase
{
    /**
     * @var Sender
     */
    private $model;

    /**
     * @var TransportBuilderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportBuilderFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->transportBuilderFactoryMock = $this->createMock(TransportBuilderFactory::class);
        $this->model = $objectManager->getObject(
            Sender::class,
            [
                'transportBuilderFactory' => $this->transportBuilderFactoryMock
            ]
        );
    }

    /**
     * Test send method
     */
    public function testSend()
    {
        $emailMetadata = [
            'template_id' => 1,
            'template_options' => ['option1', 'option2'],
            'template_variables' => ['var1', 'var2'],
            'sender_name' => 'roni',
            'sender_email' => 'roni@example.com',
            'recipient_name' => 'cost',
            'recipient_email' => 'cost@example.com'
        ];
        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateId')
            ->willReturn($emailMetadata['template_id']);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateOptions')
            ->willReturn($emailMetadata['template_options']);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateVariables')
            ->willReturn($emailMetadata['template_variables']);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderName')
            ->willReturn($emailMetadata['sender_name']);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderEmail')
            ->willReturn($emailMetadata['sender_email']);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientName')
            ->willReturn($emailMetadata['recipient_name']);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($emailMetadata['recipient_email']);

        $transportBuilderMock = $this->getMockBuilder(TransportBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'setTemplateIdentifier',
                'setTemplateOptions',
                'setTemplateVars',
                'setFrom',
                'addTo',
                'getTransport',
                'sendMessage'
            ])->getMock();
        $transportBuilderMock->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with($emailMetadata['template_id'])
            ->willReturnSelf();
        $transportBuilderMock->expects($this->once())
            ->method('setTemplateOptions')
            ->with($emailMetadata['template_options'])
            ->willReturnSelf();
        $transportBuilderMock->expects($this->once())
            ->method('setTemplateVars')
            ->with($emailMetadata['template_variables'])
            ->willReturnSelf();
        $transportBuilderMock->expects($this->once())
            ->method('setFrom')
            ->with(['name' => $emailMetadata['sender_name'], 'email' => $emailMetadata['sender_email']])
            ->willReturnSelf();
        $transportBuilderMock->expects($this->once())
            ->method('addTo')
            ->with($emailMetadata['recipient_email'], $emailMetadata['recipient_name'])
            ->willReturnSelf();
        $transportBuilderMock->expects($this->once())
            ->method('getTransport')
            ->willReturnSelf();
        $transportBuilderMock->expects($this->once())
            ->method('sendMessage')
            ->willReturnSelf();
        $this->transportBuilderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($transportBuilderMock);

        $this->model->send($emailMetadataMock);
    }
}
