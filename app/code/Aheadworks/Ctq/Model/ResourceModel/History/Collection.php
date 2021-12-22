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
namespace Aheadworks\Ctq\Model\ResourceModel\History;

use Aheadworks\Ctq\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ctq\Model\ResourceModel\History as ResourceHistory;
use Aheadworks\Ctq\Model\History;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Framework\Data\Collection\EntityFactory as FrameworkEntityFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Aheadworks\Ctq\Model\History\EntityProcessor;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 * @package Aheadworks\Ctq\Model\ResourceModel\History
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * @var EntityProcessor
     */
    private $entityProcessor;

    /**
     * @param FrameworkEntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param EntityProcessor $entityProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        FrameworkEntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EntityProcessor $entityProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->entityProcessor = $entityProcessor;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(History::class, ResourceHistory::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this
            ->attachOwnerName('admin_user', 'user_id', Owner::SELLER)
            ->attachOwnerName('customer_entity', 'entity_id', Owner::BUYER);

        /** @var History $item */
        foreach ($this as $item) {
            $this->entityProcessor->prepareDataAfterLoad($item);
        }
    }
}
