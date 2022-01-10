<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Model\ResourceModel\Form\Grid;

use DateTime;
use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\ResourceModel\Form as CustomFormResource;
use Psr\Log\LoggerInterface as Logger;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Mageplaza\CustomForm\Model\ResourceModel\Form\Grid
 */
class Collection extends SearchResult
{
    /**
     * @var array
     */
    protected $_dateRange = ['from' => null, 'to' => null];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param Data $helperData
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        Data $helperData,
        $mainTable = 'mageplaza_custom_form_form',
        $resourceModel = CustomFormResource::class
    ) {
        $this->request    = $request;
        $this->helperData = $helperData;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * @return $this|SearchResult|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['mp_cf_responses' => $this->getTable('mageplaza_custom_form_responses')],
            'main_table.id = mp_cf_responses.form_id',
            [
                'number_of_responses' => 'count(mp_cf_responses.id)',
                'ctr'                 => new Zend_Db_Expr('(count(mp_cf_responses.id) / views) * 100')
            ]
        )->group('main_table.id');

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return $this|SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'number_of_responses' || $field === 'ctr') {
            if (!empty($condition['gteq'])) {
                $this->getSelect()->having("{$field} >= ?", $condition['gteq']);
            }
            if (!empty($condition['lteq'])) {
                $this->getSelect()->having("{$field} <= ?", $condition['lteq']);
            }

            return $this;
        }

        if ($field === 'id' || $field === 'created_at') {
            $field = 'main_table.' . $field;
        }

        if ($field === 'store_ids' || $field === 'customer_group_ids') {
            $field     = 'main_table.' . $field;
            $condition = ['finset' => $condition['eq']];
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $select = clone $this->getSelect();

        $select->reset(Select::ORDER);

        return $this->getConnection()->select()->from($select, 'COUNT(*)');
    }

    /**
     * @return $this|SearchResult
     * @throws Exception
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->_applyDateRangeFilter($this->_dateRange['from'], $this->_dateRange['to']);

        return $this;
    }

    /**
     * @param null $fromDate
     * @param null $toDate
     *
     * @return $this
     * @throws Exception
     */
    protected function _applyDateRangeFilter($fromDate = null, $toDate = null)
    {
        list($fromDate, $toDate) = $this->getDateRange($fromDate, $toDate);

        if ($fromDate !== null) {
            $this->getSelect()->where('main_table.created_at >= ?', $fromDate);
        }
        if ($toDate !== null) {
            $this->getSelect()->where('main_table.created_at <= ?', $toDate);
        }

        return $this;
    }

    /**
     * @param null $fromDate
     * @param null $toDate
     * @param null $format
     *
     * @return array
     * @throws Exception
     */
    protected function getDateRange($fromDate = null, $toDate = null, $format = null)
    {
        if ($fromDate === null) {
            $fromDate = isset($this->request->getParam('mpFilter')['startDate'])
                ? $this->request->getParam('mpFilter')['startDate']
                : $this->request->getParam('startDate');
            if ($fromDate && $format) {
                $fromDate = (new DateTime($fromDate))->format($format);
            }
        }
        if ($toDate === null) {
            $toDate = isset($this->request->getParam('mpFilter')['endDate'])
                ? $this->request->getParam('mpFilter')['endDate']
                : $this->request->getParam('endDate');
            if ($toDate && $format) {
                $toDate = (new DateTime($toDate))->format($format);
            }
        }
        if ($toDate === null || $fromDate === null) {
            list($fromDate, $toDate) = $this->helperData->getDateRange($format);
        }

        return [$fromDate, $toDate];
    }
}
