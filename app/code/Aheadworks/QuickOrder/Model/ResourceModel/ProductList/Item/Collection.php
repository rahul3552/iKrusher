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
namespace Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item;

use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item as ItemResource;
use Aheadworks\QuickOrder\Model\ProductList\Item as ItemModel;
use Aheadworks\QuickOrder\Model\ProductList\Item\ObjectDataProcessor;

/**
 * Class Collection
 *
 * @package Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item
 */
class Collection extends AbstractCollection
{
    /**
     * @var ObjectDataProcessor
     */
    protected $objectDataProcessor;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ObjectDataProcessor $objectDataProcessor
     * @param AdapterInterface $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ObjectDataProcessor $objectDataProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->objectDataProcessor = $objectDataProcessor;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ItemModel::class, ItemResource::class);
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        /** @var ItemModel $item */
        foreach ($this as $item) {
            $this->objectDataProcessor->prepareDataAfterLoad($item);
        }
        return parent::_afterLoad();
    }
}
