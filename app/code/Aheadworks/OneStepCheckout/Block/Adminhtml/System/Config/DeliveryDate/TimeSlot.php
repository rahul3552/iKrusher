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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate;

use Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate\Renderer\Time;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * Class TimeSlot
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate
 */
class TimeSlot extends AbstractFieldArray
{
    /**
     * @var Time
     */
    private $startTimeOptionsRenderer;

    /**
     * @var Time
     */
    private $endTimeOptionsRenderer;

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'start_time',
            [
                'label' => __('Start Time'),
                'renderer' => $this->getStartTimeOptionsRenderer()
            ]
        );
        $this->addColumn(
            'end_time',
            [
                'label' => __('End Time'),
                'renderer' => $this->getEndTimeOptionsRenderer()
            ]
        );
        $this->_addAfter = false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $startTimeOptionsRenderer = $this->getStartTimeOptionsRenderer();
        $endTimeOptionsRenderer = $this->getEndTimeOptionsRenderer();
        $row->setData(
            'option_extra_attrs',
            [
                'option_' . $startTimeOptionsRenderer->calcOptionHash($row->getStartTime()) => 'selected="selected"',
                'option_' . $endTimeOptionsRenderer->calcOptionHash($row->getEndTime()) => 'selected="selected"'
            ]
        );
    }

    /**
     * Get start time options renderer
     *
     * @return Time
     */
    private function getStartTimeOptionsRenderer()
    {
        if (!$this->startTimeOptionsRenderer) {
            $this->startTimeOptionsRenderer = $this->createTimeOptionsRenderer();
        }
        return $this->startTimeOptionsRenderer;
    }

    /**
     * Get end time options renderer
     *
     * @return Time
     */
    private function getEndTimeOptionsRenderer()
    {
        if (!$this->endTimeOptionsRenderer) {
            $this->endTimeOptionsRenderer = $this->createTimeOptionsRenderer();
        }
        return $this->endTimeOptionsRenderer;
    }

    /**
     * Create time options renderer
     *
     * @return Time
     */
    private function createTimeOptionsRenderer()
    {
        return $this->getLayout()->createBlock(
            Time::class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );
    }
}
