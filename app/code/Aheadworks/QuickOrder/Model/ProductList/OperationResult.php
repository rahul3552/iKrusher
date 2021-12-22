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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\ProductList;

use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultMessageInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultMessageInterfaceFactory;
use Aheadworks\QuickOrder\Model\Source\QuickOrder\OperationResult\MessageType;

/**
 * Class OperationResult
 *
 * @package Aheadworks\QuickOrder\Model\ProductList
 */
class OperationResult implements OperationResultInterface
{
    /**
     * @var OperationResultMessageInterface[]
     */
    private $successMessages = [];

    /**
     * @var OperationResultMessageInterface[]
     */
    private $errorMessages = [];

    /**
     * @var string
     */
    private $lastAddedItemKey;

    /**
     * @var OperationResultMessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @param OperationResultMessageInterfaceFactory $messageFactory
     */
    public function __construct(OperationResultMessageInterfaceFactory $messageFactory)
    {
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     */
    public function getSuccessMessages()
    {
        return $this->successMessages;
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return array_merge($this->successMessages, $this->errorMessages);
    }

    /**
     * @inheritdoc
     */
    public function addSuccessMessage($title, $text)
    {
        $this->successMessages[] = $this->createMessage(MessageType::SUCCESS, $title, $text);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addErrorMessage($title, $text)
    {
        $this->errorMessages[] = $this->createMessage(MessageType::ERROR, $title, $text);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setLastAddedItemKey($lastItemKey)
    {
        $this->lastAddedItemKey = $lastItemKey;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastAddedItemKey()
    {
        return $this->lastAddedItemKey;
    }

    /**
     * Create message
     *
     * @param string $type
     * @param string $title
     * @param string $text
     * @return OperationResultMessageInterface
     */
    private function createMessage($type, $title, $text)
    {
        return $this->messageFactory->create(
            [
                OperationResultMessageInterface::TYPE => $type,
                OperationResultMessageInterface::TITLE => $title,
                OperationResultMessageInterface::TEXT => $text
            ]
        );
    }
}
