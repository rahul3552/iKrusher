<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml;

/**
 * Block responsible for rendering error message to in Message Queue
 */
class ErrorReport extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var \I95DevConnect\MessageQueue\Model\ErrorUpdateData
     */

    public $modelErrorUpdateDataFactory;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $modelErrorUpdateDataFactory
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $modelErrorUpdateDataFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {

        $this->modelErrorUpdateDataFactory = $modelErrorUpdateDataFactory;
        $this->backendUrl = $backendUrl;
    }

    /**
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $baseAdminUrl = $this->backendUrl->getUrl("messagequeue/messagequeue/errorData");
        $loadData = $row->getData();
        $errorId = (int) $loadData['error_id'];
        if ($loadData['status']== \I95DevConnect\MessageQueue\Helper\Data::ERROR && $errorId !== 0) {
                return '<a herf onclick="(function () {require('.
                        "'massagequeuegrid').showErrorMessage('$errorId', '$baseAdminUrl');})();".
                        '" return false">' . 'Error ' . '</a>';
        } elseif ($loadData['status'] != \I95DevConnect\MessageQueue\Helper\Data::PENDING) {
            return $loadData[$this->getColumn()->getIndex()];
        } else {
            return '0';
        }
    }
}
