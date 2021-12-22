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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Report;

use Aheadworks\OneStepCheckout\Model\Report\Indexer\Action\Full as FullAction;
use Aheadworks\OneStepCheckout\Model\Report\Indexer\Action\FullFactory as FullActionFactory;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

/**
 * Class Indexer
 * @package Aheadworks\OneStepCheckout\Model\Report
 */
class Indexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var FullActionFactory
     */
    private $fullActionFactory;

    /**
     * @param FullActionFactory $fullActionFactory
     */
    public function __construct(
        FullActionFactory $fullActionFactory
    ) {
        $this->fullActionFactory = $fullActionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function executeFull()
    {
        /** @var FullAction $action */
        $action = $this->fullActionFactory->create();
        $action->execute();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     */
    public function execute($ids)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     */
    public function executeList(array $ids)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     */
    public function executeRow($id)
    {
    }
}
