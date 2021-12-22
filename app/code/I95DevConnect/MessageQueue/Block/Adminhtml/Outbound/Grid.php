<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\Outbound;

use \I95DevConnect\MessageQueue\Helper\Data as MessageQueueHelper;

/**
 * Outbound Message Queue Grid
 */
class Grid extends \I95DevConnect\MessageQueue\Block\Adminhtml\MessageQueueGrid
{

    public $i95DevMagentoMQ;
    public $messageQueueHelper;
    public $status =  [
        ""=>"All",
        MessageQueueHelper::PENDING => "Pending",
        MessageQueueHelper::PROCESSING => "Processing",/** @updatedBy Sravani Polu change label **/
        MessageQueueHelper::ERROR => "Error",
        MessageQueueHelper::SUCCESS => "Request Transferred",/** @updatedBy Sravani Polu change label **/
        MessageQueueHelper::COMPLETE => "Complete",
    ];

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterface $i95DevMagentoMQ
     * @param \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterface $i95DevMagentoMQ,
        \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->i95DevMagentoMQ = $i95DevMagentoMQ;
        $this->messageQueueHelper = $messageQueueHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setID('magentoMessageQueueGrid');
        $this->setDefaultSort(self::MSG_ID);
        $this->setDefaultDir('DESC');
        $this->entityList = array_merge(["" => "All"], $this->showInOutbound());
        unset($this->entityList["address"]);
        /* @updatedBy Debashis. If component is NAV/BC no need to show customer group in out bound mq */
        $component = $this->messageQueueHelper->getComponent();
        if ($component == 'NAV' || $component  == 'BC') {
            unset($this->entityList["CustomerGroup"]);
        }
    }

    /**
     * Prepare grid collection object
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->i95DevMagentoMQ->getCollection();
        //@author Divya Koona. Added to exclude Customer Group entity data from OBMQ collection for NAV
        $supportedEntity = $this->showInOutbound();
        $supportedEntityCodes = array_keys($supportedEntity);
        $collection->addFieldToFilter(self::ENTITYCODE, ['in' => $supportedEntityCodes]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * fetch the entity list to show in outbound mesagequeue
     * @return array
     * @author Arushi Bansal
     */
    protected function showInOutbound()
    {
        $supportedEntity = $this->messageQueueHelper->getEntityTypeOutboundList();
        $existingEntity = $this->i95DevMagentoMQ->getCollection();
        $existingEntity->addFieldToSelect(self::ENTITYCODE)->getSelect()->group(self::ENTITYCODE);
        $existingEntity = $existingEntity->getData();
        $allEntityList = $this->messageQueueHelper->getEntityTypeList();

        foreach ($existingEntity as $entity_code) {
            //@author Divya Koona. Added to exclude Customer Group entity from OBMQ Entity Drop Down for NAV
            if (!empty($entity_code[self::ENTITYCODE]) && in_array($entity_code[self::ENTITYCODE], $supportedEntity)) {
                $supportedEntity[$entity_code[self::ENTITYCODE]] = $allEntityList[$entity_code[self::ENTITYCODE]];
            }
        }

        return array_unique($supportedEntity);
    }

    /**
     * Prepare default grid column
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'erp_code',
            [
                self::HEADER => "ERP",
                self::INDEX => 'erp_code'
            ]
        );
      
        $this->addColumn(
            'magento_id',
            [
                self::HEADER => __('Magento Id'),
                'type' => 'text',
                self::INDEX => 'magento_id',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );

        $this->addColumn(
            'target_id',
            [
                self::HEADER => "ERP Id",
                'type' => 'text',
                self::INDEX => 'target_id',
                'renderer' => 'I95DevConnect\MessageQueue\Block\Adminhtml\ErrorReport',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );

        $this->addColumn(
            'updated_by',
            [
                self::HEADER => __('Updated By'),
                'type' => 'text',
                self::INDEX => 'updated_by',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );
        return $this;
    }

    /**
     * Initialize grid before rendering
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareGrid()
    {
        $this->_prepareColumns();
        parent::_prepareGrid();
        return $this;
    }

    /**
     * get status message
     * @param string $value
     * @param Object $row
     * @return string
     */
    public function getMessageStatus($value, $row)
    {
        return '<span class="status_' . $row->getID() . '" >' . $value . "</span>";
    }
}
