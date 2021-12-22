<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml;

/**
 * Class for outbound summary report
 */
class OutboundSummary extends SummaryReport
{

    public $_template = 'I95DevConnect_MessageQueue::outboundsummary.phtml';
    public $totalStatusRcords = [];
    public $messageQueueHelper;
    public $i95DevMagentoMQ;
    public $entityListReader;
    public $backendUrl;

    /**
     * OutboundSummary constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper
     * @param \I95DevConnect\MessageQueue\Model\I95DevMagentoMQRepositoryFactory $i95DevMagentoMQ
     * @param \I95DevConnect\MessageQueue\Model\ReadCustomXml $entityListReader
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper,
        \I95DevConnect\MessageQueue\Model\I95DevMagentoMQRepositoryFactory $i95DevMagentoMQ,
        \I95DevConnect\MessageQueue\Model\ReadCustomXml $entityListReader,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {
        $this->messageQueueHelper = $messageQueueHelper;
        $this->i95DevMagentoMQ = $i95DevMagentoMQ;
        $this->entityListReader = $entityListReader;
        $this->backendUrl = $backendUrl;
        parent::__construct($context, $data);
    }

    /**
     * get url of outbound MQ
     * @return string
     */
    public function getOutboundMessageQueue()
    {
        return $this->backendUrl->getUrl("messagequeue/messagequeue/magento");
    }

    /**
     * get MQ Url
     * @param string $filter
     * @return string
     */

    public function getMessageQUrl($filter)
    {
        $filter = base64_encode($filter);
        return $this->backendUrl->getUrl('messagequeue/messagequeue/magento', ['filter' => $filter]);
    }

    /**
     * get reports
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityWiseSyncReport()
    {
        $totalReport = [];
        $entityList = $this->entityListReader->getXmlDataOrderBySortOrder();
        if (!empty($entityList)) {
            $this->totalStatusRcord[self::PENDING] = 0;
            $this->totalStatusRcord[self::PROCESSING] = 0;
            $this->totalStatusRcord[self::ERROR] = 0;
            $this->totalStatusRcord[self::SUCCESS] = 0;
            $this->totalStatusRcord[self::COMPLETE] = 0;
            $this->totalStatusRcord[self::TOTAL] = 0;
            foreach ($entityList as $entityCode => $entity) {
                if ($entityCode == "address") {
                    continue;
                }
                $modelCollection = $this->i95DevMagentoMQ->create()->getCollection();
                $modelCollection->addFieldToFilter("entity_code", $entity['id']);
                $modelCollection->removeAllFieldsFromSelect();
                $modelCollection->removeFieldFromSelect("msg_id");
                $modelCollection->addExpressionFieldToSelect(
                    self::PENDING,
                    "(count(if(status = '1', 1, null)) )",
                    self::PENDING
                );
                $modelCollection->addExpressionFieldToSelect(
                    self::PROCESSING,
                    "(count(if(status = '2', 1, null)) )",
                    self::PROCESSING
                );
                $modelCollection->addExpressionFieldToSelect(
                    self::ERROR,
                    "(count(if(status = '3', 1, null)) )",
                    self::ERROR
                );
                $modelCollection->addExpressionFieldToSelect(
                    self::SUCCESS,
                    "(count(if(status = '4', 1, null)) )",
                    self::SUCCESS
                );
                $modelCollection->addExpressionFieldToSelect(
                    self::COMPLETE,
                    "(count(if(status = '5', 1, null)) )",
                    self::COMPLETE
                );
                $report = $this->getTotalReports($modelCollection, $entity);

                if (!empty($report)) {
                    $totalReport[] = $report;
                }
            }
            $this->totalStatusRcords = $this->totalStatusRcord;
        }
        return $totalReport;
    }
}
