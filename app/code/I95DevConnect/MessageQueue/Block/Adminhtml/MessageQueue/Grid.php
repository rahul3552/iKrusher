<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\MessageQueue;

use \I95DevConnect\MessageQueue\Helper\Data as MessageQueueHelper;

/**
 * Inbound Message Queue Grid
 */
class Grid extends \I95DevConnect\MessageQueue\Block\Adminhtml\MessageQueueGrid
{
    public $status = [
        ""=>"All",
        MessageQueueHelper::PENDING => "Pending",
        MessageQueueHelper::PROCESSING => "Processing",
        MessageQueueHelper::ERROR => "Error",
        MessageQueueHelper::SUCCESS => "Success",
        MessageQueueHelper::COMPLETE => "Complete",
    ];

    public $messageQueueHelper;
    public $i95DevErpMQRepository;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param array $data
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {

        $this->messageQueueHelper=$messageQueueHelper;
        $this->i95DevErpMQRepository = $i95DevErpMQRepository;
        $this->backendUrl = $backendUrl;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setID('messageQueueGrid');
        $this->setDefaultSort(self::MSG_ID);
        $this->setDefaultDir('DESC');
        $this->entityList = $this->showInInbound();
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->i95DevErpMQRepository->create()->getCollection();
        //@author Divya Koona. Added to exclude Customer Group entity data from IBMQ collection for NAV

        $supportedEntityCodes = array_keys($this->entityList);
        $collection->addFieldToFilter(self::ENTITYCODE, ['in' => $supportedEntityCodes]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * fetch the entity list to show in inbound mesagequeue
     * @return array
     * @author Arushi Bansal
     */

    protected function showInInbound()
    {

        $supportedEntity = $this->messageQueueHelper->getEntityTypeInboundList();
        $existingEntity = $this->i95DevErpMQRepository->create()->getCollection();
        $existingEntity->addFieldToSelect(self::ENTITYCODE)->getSelect()->group(self::ENTITYCODE);
        $existingEntity = $existingEntity->getData();
        $allEntityList = $this->messageQueueHelper->getEntityTypeList();

        foreach ($existingEntity as $entity_code) {
            //@author Divya Koona. Added to exclude Customer Group entity from IBMQ Entity Drop Down for NAV
            if (!empty($entity_code[self::ENTITYCODE]) && in_array($entity_code[self::ENTITYCODE], $supportedEntity)) {
                $supportedEntity[$entity_code[self::ENTITYCODE]] = __($allEntityList[$entity_code[self::ENTITYCODE]]);
            }
        }

        return array_merge([""=>"All"], $supportedEntity);
    }

    /**
     * Prepare default grid column
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'target_id',
            [
                self::HEADER => "ERP Id",
                'type' => 'text',
                self::INDEX => 'target_id',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );

        $this->addColumn(
            'ref_name',
            [
                self::HEADER => "Reference Name",
                'type' => 'text',
                self::INDEX => 'ref_name',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );

        $this->addColumn(
            'counter',
            [
                self::HEADER => __('Count'),
                'type' => 'number',
                self::INDEX => 'counter',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );
        $this->addColumn(
            'magento_id',
            [
                self::HEADER => __('Response'),
                'type' => 'text',
                self::INDEX => 'magento_id',
                'renderer' => 'I95DevConnect\MessageQueue\Block\Adminhtml\ErrorReport',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );

        $this->addColumn(
            'action',
            [
                self::HEADER => __('Data'),
                'width' => '50px',
                'frame_callback' => [$this, 'getDataHtml'],
                'filter' => false,
                'sortable' => false,
                self::INDEX => 'data_id',
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
        $this->_prepareEntityListLayout();
        parent::_prepareGrid();

        return $this;
    }

    /**
     * Prepare Entititylist widget.
     */
    public function _prepareEntityListLayout()
    {
        $block = $this->getLayout()->createBlock('I95DevConnect\MessageQueue\Block\Adminhtml\Widget\Entitylist');
        $this->setChild('entititylist', $block);
    }

    /**
     * get main buttons
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->getChildHtml('entititylist');
        if ($this->getFilterVisibility()) {
            $html.= $this->getSearchButtonHtml();
            $html.= $this->getResetFilterButtonHtml();
        }

        return $html;
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

    /**
     * get data html
     *
     * @param string $value
     * @param Object $row
     *
     * @return string
     */
    public function getDataHtml($value, $row)
    {
        $dataUrl = $this->backendUrl->getUrl("messagequeue/messagequeue/data");

        $msgId = $row->getMsgId();
        $messageQueue = $this->i95DevErpMQRepository->create()->get($msgId);
        if ($messageQueue->getDataId()) {
            return '<a herf=javascript:void(0) onclick="(function () {require('."'massagequeuegrid'".
                    ').showDataMessage('."'".$messageQueue->getDataId()."'".', `'.$dataUrl.'`);})();" >View</a>';
        } else {
            return 'No Data';
        }
    }

    /**
     * Prepare mass action
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIDField(self::MSG_ID);
        $this->getMassactionBlock()->setFormFieldName(self::MSG_ID);

        $this->getMassactionBlock()->addItem(
            'sync',
            [
                'label' => __('Sync'),
                'url' => $this->getUrl('messagequeue/*/massSync')
            ]
        );
        return $this;
    }
}
