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

/**
 * Class ActionManagement
 * @package Aheadworks\Ctq\Model\Quote\Action
 */
class ActionManagement
{
    /**
     * @var Pool
     */
    private $actionPool;

    /**
     * @param Pool $actionPool
     */
    public function __construct(
        Pool $actionPool
    ) {
        $this->actionPool = $actionPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionObjects($actions)
    {
        $actionObjects = [];
        foreach ($actions as $action) {
            $actionObjects[] = $this->actionPool->getAction($action);
        }
        $actionObjects = $this->sort($actionObjects);

        return $actionObjects;
    }

    /**
     * Sorting modifiers according to sort order
     *
     * @param array $data
     * @return array
     */
    protected function sort(array $data)
    {
        usort($data, function ($a, $b) {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });

        return $data;
    }
}
