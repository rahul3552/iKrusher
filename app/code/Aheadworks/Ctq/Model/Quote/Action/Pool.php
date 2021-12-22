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
namespace Aheadworks\Ctq\Model\Quote\Action;

use Aheadworks\Ctq\Api\Data\QuoteActionInterface;
use Aheadworks\Ctq\Api\Data\QuoteActionInterfaceFactory;

/**
 * Class Pool
 * @package Aheadworks\Ctq\Model\Quote\Action
 */
class Pool
{
    /**
     * @var QuoteActionInterfaceFactory
     */
    private $actionFactory;

    /**
     * @var array
     */
    private $actions = [];

    /**
     * @var array
     */
    private $actionsInstance = [];

    /**
     * @param QuoteActionInterfaceFactory $actionFactory
     * @param array $actions
     */
    public function __construct(
        QuoteActionInterfaceFactory $actionFactory,
        array $actions
    ) {
        $this->actionFactory = $actionFactory;
        $this->actions = $actions;
    }

    /**
     * Retrieves restrictions instance
     *
     * @param string $code
     * @return QuoteActionInterface
     * @throws \Exception
     */
    public function getAction($code)
    {
        if (!isset($this->actionsInstance[$code])) {
            if (!isset($this->actions[$code])) {
                throw new \Exception(sprintf('Unknown action: %s requested', $code));
            }
            $instance = $this->actionFactory->create(['data' => $this->actions[$code]]);
            $this->actionsInstance[$code] = $instance;
        }
        return $this->actionsInstance[$code];
    }
}
