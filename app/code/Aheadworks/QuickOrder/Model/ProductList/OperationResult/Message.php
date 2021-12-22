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
namespace Aheadworks\QuickOrder\Model\ProductList\OperationResult;

use Aheadworks\QuickOrder\Api\Data\OperationResultMessageInterface;

/**
 * Class Message
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\OperationResult
 */
class Message implements OperationResultMessageInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var $text
     */
    private $text;

    /**
     * @param string $type
     * @param string $title
     * @param string $text
     */
    public function __construct($type, $title, $text)
    {
        $this->type = $type;
        $this->title = $title;
        $this->text = $text;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return $this->text;
    }
}
