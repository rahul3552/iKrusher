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

namespace Mageplaza\CustomForm\Block\Adminhtml\Form;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\CustomForm\Model\Form;

/**
 * Class Edit
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Form edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mageplaza_CustomForm';
        $this->_controller = 'adminhtml_form';

        parent::_construct();

        $this->buttonList->add('save-and-continue', [
            'label'          => __('Save and Continue Edit'),
            'class'          => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event'  => 'saveAndContinueEdit',
                        'target' => '#edit_form'
                    ]
                ]
            ]
        ], -80);

        /** @var Form $form */
        $form = $this->coreRegistry->registry('mageplaza_custom_form_form');

        if ($form->getId()) {
            $this->buttonList->add('copy', [
                'label'   => __('Duplicate'),
                'class'   => 'save',
                'onclick' => sprintf("location.href = '%s';", $this->getCopyUrl()),
            ], -70);
        }
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        /** @var Form $form */
        $form = $this->coreRegistry->registry('mageplaza_custom_form_form');
        if ($id = $form->getId()) {
            return $this->getUrl('*/*/save', ['id' => $id]);
        }

        return parent::getFormActionUrl();
    }

    /**
     * Get duplicate action URL
     *
     * @return string
     */
    protected function getCopyUrl()
    {
        /** @var Form $form */
        $form = $this->coreRegistry->registry('mageplaza_custom_form_form');
        $this->_backendSession->setCopyData($form->getData());

        return $this->getUrl('*/*/edit', ['id' => 'copy']);
    }
}
