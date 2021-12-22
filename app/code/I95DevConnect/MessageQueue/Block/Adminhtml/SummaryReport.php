<?php

namespace I95DevConnect\MessageQueue\Block\Adminhtml;

/**
 * summary report for inbound messagequeue
 */
class SummaryReport extends \Magento\Backend\Block\Template
{
    const PENDING = "pending";
    const PROCESSING = "processing";
    const ERROR = "error";
    const SUCCESS = "success";
    const COMPLETE = "complete";
    const TOTAL = "total";
    public $totalStatusRcords = [];
    public $totalStatusRcord = [];

    /**
     * @param $modelCollection
     * @param $entity
     * @return array|mixed
     */
    protected function getTotalReports($modelCollection, $entity)
    {
        $modelCollection->addExpressionFieldToSelect(self::TOTAL, "(count(*))", self::TOTAL);
        if ($modelCollection->getSize() > 0) {
            $report = $modelCollection->getData()[0];
            $this->totalStatusRcord[self::PENDING]+=$report[self::PENDING];
            $this->totalStatusRcord[self::PROCESSING]+=$report[self::PROCESSING];
            $this->totalStatusRcord[self::ERROR]+=$report[self::ERROR];
            $this->totalStatusRcord[self::SUCCESS]+=$report[self::SUCCESS];
            $this->totalStatusRcord[self::COMPLETE]+=$report[self::COMPLETE];
            $this->totalStatusRcord[self::TOTAL]+=$report[self::TOTAL];
            $report['entity'] = $entity['title'];
            $report['entity_code'] = $entity['id'];
            return $report;
        } else {
            return [];
        }
    }

    /**
     * get total status records
     * @return array
     */
    public function getTotalStatusRcords()
    {
        return $this->totalStatusRcords;
    }
}
