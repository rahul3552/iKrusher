<?php

namespace I95DevConnect\MessageQueue\Block\Adminhtml;

use I95DevConnect\MessageQueue\Block\Adminhtml\MessageQueue\Grid;

/**
 * messagequeue grid block class for outbound messagequeue
 */
class MessageQueueGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    public $status = [];
    const OPTIONS = "options";
    const ENTITYCODE = "entity_code";
    const MSG_ID = "msg_id";
    const INDEX = "index";
    const COL_ID = "col-id";
    const COLUMN_CSS_CLASS = "column_css_class";
    const HEADER_CSS_CLASS = "header_css_class";
    const STATUS = "status";
    const HEADER = "header";
    public $entityList;

    /**
     * Prepare default grid column
     *
     * @return void
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            self::MSG_ID,
            [
                self::HEADER => __('Message ID'),
                'type' => 'number',
                self::INDEX => self::MSG_ID,
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );
        $this->addColumn(
            self::ENTITYCODE,
            [
                self::HEADER => "Entity",
                'type' => self::OPTIONS,
                self::INDEX => self::ENTITYCODE,
                self::OPTIONS => $this->entityList
            ]
        );

        $this->addColumn(
            'created_dt',
            [
                self::HEADER => __('Created Date'),
                'type' => 'datetime',
                self::INDEX => 'created_dt',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );

        $this->addColumn(
            'updated_dt',
            [
                self::HEADER => __('Updated Date'),
                'type' => 'datetime',
                self::INDEX => 'updated_dt',
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID
            ]
        );

        $this->addColumn(
            self::STATUS,
            [
                self::HEADER => __('Status'),
                'type' => self::OPTIONS,
                self::INDEX => self::STATUS,
                'frame_callback' => [$this, 'getMessageStatus'],
                self::HEADER_CSS_CLASS => self::COL_ID,
                self::COLUMN_CSS_CLASS => self::COL_ID,
                self::OPTIONS => $this->status
            ]
        );
    }
}
