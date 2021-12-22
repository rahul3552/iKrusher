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
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Report\CheckoutBehavior;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\FilterableInterface;
use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\CheckoutBehavior as CheckoutBehaviorResource;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Framework\App\ResourceConnection as DefaultResource;

/**
 * Class Collection
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\Report\CheckoutBehavior
 */
class Collection extends AbstractCollection implements FilterableInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * @var DefaultResource
     */
    private $defaultResource;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param OrderResource $orderResource
     * @param DefaultResource $defaultResource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        OrderResource $orderResource,
        DefaultResource $defaultResource
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
        $this->orderResource = $orderResource;
        $this->defaultResource = $defaultResource;
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, CheckoutBehaviorResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $connection = $this->getConnection();

        $completedCountExpr = new \Zend_Db_Expr('SUM(main_table.is_completed)');
        $totalCountExpr = new \Zend_Db_Expr('COUNT(main_table.quote_id)');
        $this->getSelect()
            ->from(
                ['main_table' => $this->getMainTable()],
                [
                    'field_name' => 'main_table.field_name',
                    'scope' => 'main_table.scope',
                    'completed' => $completedCountExpr,
                    'total' => $totalCountExpr,
                    'completed_percent' => new \Zend_Db_Expr('100 / ' . $totalCountExpr . ' * ' . $completedCountExpr)
                ]
            )
            ->join(
                ['quote_table' => $this->getTable('quote')],
                'main_table.quote_id = quote_table.entity_id'
                . ' AND (quote_table.is_active = 1) AND (quote_table.items_count > 0)',
                [
                    'period' => $connection->getCheckSql(
                        'quote_table.created_at > quote_table.updated_at',
                        'DATE(quote_table.created_at)',
                        'DATE(quote_table.updated_at)'
                    )
                ]
            )
            ->group(['field_name', 'scope']);

        return $this;
    }

    /**
     * Get totals items
     *
     * @return array
     */
    public function getTotalsItems()
    {
        $connection = $this->getConnection();
        $salesConnection = $this->orderResource->getConnection();

        $rawSelect = clone $this->getSelect();
        $rawSelect
            ->reset(Select::COLUMNS)
            ->reset(Select::GROUP);

        $abandonedSelect = clone $rawSelect;
        $fromPart = $abandonedSelect->getPart(Select::FROM);
        $fromPart['quote_table']['joinCondition'] = 'main_table.quote_id = quote_table.entity_id'
            . ' AND (quote_table.is_active = 1) AND (quote_table.items_count > 0)';
        $abandonedSelect->setPart(Select::FROM, $fromPart);
        $abandonedSelect->columns(
            [
                'abandoned_cart' => 'quote_table.entity_id',
                'completed_cart' => new \Zend_Db_Expr('NULL'),
                'period' => $connection->getCheckSql(
                    'quote_table.created_at > quote_table.updated_at',
                    'DATE(quote_table.created_at)',
                    'DATE(quote_table.updated_at)'
                )
            ]
        );

        $completedSelect = clone $rawSelect;
        $fromPart = $completedSelect->getPart(Select::FROM);
        $fromPart['quote_table']['joinCondition'] = 'main_table.quote_id = quote_table.entity_id'
            . ' AND (quote_table.is_active = 0) AND (quote_table.items_count > 0)';
        $completedSelect->setPart(Select::FROM, $fromPart);
        $completedSelect->columns(
            [
                'abandoned_cart' => new \Zend_Db_Expr('NULL'),
                'completed_cart' => 'quote_table.entity_id',
                'period' => $connection->getCheckSql(
                    'quote_table.created_at > quote_table.updated_at',
                    'DATE(quote_table.created_at)',
                    'DATE(quote_table.updated_at)'
                )
            ]
        )->join(
            ['order_table' => $this->orderResource->getTable('sales_order')],
            'main_table.quote_id = order_table.quote_id',
            [],
            $salesConnection->getConfig()['dbname']
        );

        $subSelect = $connection->select()->union([$abandonedSelect, $completedSelect]);
        $abandonedCheckoutsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(abandoned_cart), 0)');
        $completedCheckoutsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(completed_cart), 0)');
        $totalsSelect = $connection->select()->from(
            ['main_table' => $subSelect],
            [
                'abandoned_checkouts_count' => $abandonedCheckoutsCountExpr,
                'completed_checkouts_count' => $completedCheckoutsCountExpr,
                'abandoned_checkout_rate' => new \Zend_Db_Expr(
                    'COALESCE((100 / (' . $abandonedCheckoutsCountExpr . ' + ' . $completedCheckoutsCountExpr
                    . ')) * ' . $abandonedCheckoutsCountExpr . ', 0)'
                ),
            ]
        );

        return $connection->fetchRow($totalsSelect) ? : [];
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerGroupIdFilter($groupId)
    {
        $this->addFilter('quote_table.customer_group_id', ['eq' => $groupId], 'public');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addStoreIdFilter($storeId)
    {
        $this->addFilter('quote_table.store_id', ['eq' => $storeId], 'public');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addStoreGroupIdFilter($storeGroupId)
    {
        $this->addFilter('store_table.group_id', ['eq' => $storeGroupId], 'public');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addWebsiteIdFilter($websiteId)
    {
        $this->addFilter('store_table.website_id', ['eq' => $websiteId], 'public');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPeriodFilter($periodFrom, $periodTo)
    {
        $periodExpr = $this->getConnection()->getCheckSql(
            'quote_table.created_at > quote_table.updated_at',
            'DATE(quote_table.created_at)',
            'DATE(quote_table.updated_at)'
        );
        $this->addFilter($periodExpr, ['gteq' => $periodFrom], 'public');
        $this->addFilter($periodExpr, ['lteq' => $periodTo], 'public');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreTable();
        parent::_renderFiltersBefore();
    }

    /**
     * Join store table
     *
     * @return $this
     */
    private function joinStoreTable()
    {
        $defaultConnection = $this->defaultResource->getConnection(DefaultResource::DEFAULT_CONNECTION);
        if (!$this->getFlag('store_table_joined')
            && ($this->getFilter('store_table.group_id') || $this->getFilter('store_table.website_id'))
        ) {
            $this->getSelect()->joinLeft(
                ['store_table' => $this->getTable('store')],
                'quote_table.store_id = store_table.store_id',
                ['*'],
                $defaultConnection->getConfig()['dbname']
            );
            $this->setFlag('store_table_joined', true);
        }
        return $this;
    }
}
