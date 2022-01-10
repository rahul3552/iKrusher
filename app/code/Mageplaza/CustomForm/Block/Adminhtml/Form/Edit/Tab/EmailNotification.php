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

namespace Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Email\Identity as EmailIdentity;
use Magento\Config\Model\Config\Source\Email\Template as EmailTemplate;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab\Renderer\EmailPlaning;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Config\Source\SendTime;

/**
 * Class EmailNotification
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab
 */
class EmailNotification extends Generic implements TabInterface
{
    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var EmailIdentity
     */
    protected $emailIdentity;

    /**
     * @var EmailTemplate
     */
    protected $emailTemplate;

    /**
     * @var SendTime
     */
    protected $sendTime;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * EmailNotification constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesno
     * @param EmailIdentity $emailIdentity
     * @param EmailTemplate $emailTemplate
     * @param SendTime $sendTime
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesno,
        EmailIdentity $emailIdentity,
        EmailTemplate $emailTemplate,
        SendTime $sendTime,
        Data $helperData,
        array $data = []
    ) {
        $this->yesno         = $yesno;
        $this->emailIdentity = $emailIdentity;
        $this->emailTemplate = $emailTemplate;
        $this->sendTime      = $sendTime;
        $this->helperData    = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\CustomForm\Model\Form $customForm */
        $customForm = $this->_coreRegistry->registry('mageplaza_custom_form_form');
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('form_');
        $form->setFieldNameSuffix('form');

        $fieldset = $form->addFieldset('admin_nof_fieldset', [
            'legend' => __('Admin Notification'),
            'class'  => 'fieldset-wide'
        ]);

        $adminNofEnabledChecked =
            $customForm->getAdminNofEnabled() === null
            || $customForm->getAdminNofEnabled() === '2'
                ? 'checked' : '';
        if ($adminNofEnabledChecked) {
            $customForm->setAdminNofEnabled($this->helperData->getAdminNofEnabled());
        }

        $fieldset->addField('admin_nof_enabled', 'select', [
            'name'               => 'admin_nof_enabled',
            'label'              => __('Enable'),
            'title'              => __('Enable'),
            'values'             => $this->yesno->toOptionArray(),
            'after_element_html' => $this->getUseConfigHtml('admin_nof_enabled', $adminNofEnabledChecked, 2),
        ]);

        $adminNofSendToChecked =
            !$customForm->getId()
            || $customForm->getAdminNofSendTo() === 'mp-use-config'
                ? 'checked' : '';
        if ($adminNofSendToChecked) {
            $customForm->setAdminNofSendTo($this->helperData->getAdminNofSendTo());
        }

        $fieldset->addField('admin_nof_send_to', 'textarea', [
            'name'               => 'admin_nof_send_to',
            'label'              => __('Send To'),
            'title'              => __('Send To'),
            'note'               => 'Separated by comma(,).',
            'container_id'       => 'row_form_admin_nof_send_to',
            'after_element_html' =>
                '<label class="mp-use-config-label addafter" for="admin-nof-send-to-use-config">' .
                '<input type="checkbox"
                class="mp-use-config"
                id="admin-nof-send-to-use-config"
                name="form[admin_nof_send_to]"
                value="mp-use-config" ' . $adminNofSendToChecked . '>' .
                __('Use Config') .
                '</label>',
        ]);

        $fieldset->addField('admin_nof_send_time', 'select', [
            'name'   => 'admin_nof_send_time',
            'label'  => __('Send Time'),
            'title'  => __('Send Time'),
            'values' => $this->sendTime->toOptionArray(),
        ]);

        $adminNofSenderChecked =
            $customForm->getAdminNofSender() === null
            || $customForm->getAdminNofSender() === 'mp-use-config'
                ? 'checked' : '';
        if ($adminNofSenderChecked) {
            $customForm->setAdminNofSender($this->helperData->getAdminNofSender());
        }

        $fieldset->addField('admin_nof_sender', 'select', [
            'name'               => 'admin_nof_sender',
            'label'              => __('Sender'),
            'title'              => __('Sender'),
            'values'             => $this->emailIdentity->toOptionArray(),
            'container_id'       => 'row_form_admin_nof_sender',
            'after_element_html' => $this->getUseConfigHtml('admin_nof_sender', $adminNofSenderChecked),
        ]);

        $adminNofTemplateChecked =
            $customForm->getAdminNofTemplate() === null
            || $customForm->getAdminNofTemplate() === 'mp-use-config'
                ? 'checked' : '';
        if ($adminNofTemplateChecked) {
            $customForm->setAdminNofTemplate($this->helperData->getAdminNofEmailTemplate());
        }

        $fieldset->addField('admin_nof_template', 'select', [
            'name'               => 'admin_nof_template',
            'label'              => __('Email Template'),
            'title'              => __('Email Template'),
            'values'             => $this->emailTemplate
                ->setPath('mp_custom_form/admin_notification_email_template')->toOptionArray(),
            'container_id'       => 'row_form_admin_nof_template',
            'after_element_html' => $this->getUseConfigHtml('admin_nof_template', $adminNofTemplateChecked),
        ]);

        $adminNofCcToEmailChecked =
            !$customForm->getId()
            || $customForm->getAdminNofCcToEmail() === 'mp-use-config'
                ? 'checked' : '';
        if ($adminNofCcToEmailChecked) {
            $customForm->setAdminNofCcToEmail($this->helperData->getAdminCCEmail());
        }

        $fieldset->addField('admin_nof_cc_to_email', 'textarea', [
            'name'               => 'admin_nof_cc_to_email',
            'label'              => __('CC To Emails'),
            'title'              => __('CC To Emails'),
            'note'               => 'Separated by comma(,).',
            'container_id'       => 'row_form_admin_nof_cc_to_email',
            'after_element_html' =>
                '<label class="mp-use-config-label addafter" for="admin-nof-send-to-use-config">' .
                '<input type="checkbox"
                class="mp-use-config"
                id="admin-nof-send-to-use-config"
                name="form[admin_nof_cc_to_email]"
                value="mp-use-config" ' . $adminNofCcToEmailChecked . '>' .
                __('Use Config') .
                '</label>',
        ]);

        $adminNofBccToEmailChecked =
            !$customForm->getId()
            || $customForm->getAdminNofBccToEmail() === 'mp-use-config'
                ? 'checked' : '';
        if ($adminNofBccToEmailChecked) {
            $customForm->setAdminNofBccToEmail($this->helperData->getAdminBCCEmail());
        }

        $fieldset->addField('admin_nof_bcc_to_email', 'textarea', [
            'name'               => 'admin_nof_bcc_to_email',
            'label'              => __('BCC To Emails'),
            'title'              => __('BCC To Emails'),
            'note'               => 'Separated by comma(,).',
            'container_id'       => 'row_form_admin_nof_bcc_to_email',
            'after_element_html' =>
                '<label class="mp-use-config-label addafter" for="admin-nof-send-to-use-config">' .
                '<input type="checkbox"
                class="mp-use-config"
                id="admin-nof-send-to-use-config"
                name="form[admin_nof_bcc_to_email]"
                value="mp-use-config" ' . $adminNofBccToEmailChecked . '>' .
                __('Use Config') .
                '</label>',
        ]);

        $adminNofAttachedFiles =
            $customForm->getAdminNofAttachedFiles() === null
            || $customForm->getAdminNofAttachedFiles() === '2'
                ? 'checked' : '';
        if ($adminNofAttachedFiles) {
            $customForm->setAdminNofAttachedFiles($this->helperData->getAdminAttachedFile());
        }

        $fieldset->addField('admin_nof_attached_files', 'select', [
            'name'               => 'admin_nof_attached_files',
            'label'              => __('Attach Uploads File to Email'),
            'title'              => __('Attach Uploads File to Email'),
            'values'             => $this->yesno->toOptionArray(),
            'note'               => __('If yes, will attach uploaded files to the notification email sent to the admin.'),
            'after_element_html' => $this->getUseConfigHtml('admin_nof_attached_files', $adminNofAttachedFiles, 2),
        ]);

        $autoResFieldset = $form->addFieldset('auto_res_fieldset', [
            'legend' => __('Auto-responder'),
            'class'  => 'fieldset-wide'
        ]);

        $autoResEnabledChecked = $customForm->getAutoResEnabled() === null
        || $customForm->getAutoResEnabled() === '2'
            ? 'checked' : '';
        if ($autoResEnabledChecked) {
            $customForm->setAutoResEnabled($this->helperData->getCustomerNofEnabled());
        }

        $autoResFieldset->addField('auto_res_enabled', 'select', [
            'name'               => 'auto_res_enabled',
            'label'              => __('Enable'),
            'title'              => __('Enable'),
            'values'             => $this->yesno->toOptionArray(),
            'after_element_html' => $this->getUseConfigHtml('auto_res_enabled', $autoResEnabledChecked, 2),
        ]);

        $autoResSenderChecked =
            $customForm->getAutoResSender() === null
            || $customForm->getAutoResSender() === 'mp-use-config'
                ? 'checked' : '';
        if ($autoResSenderChecked) {
            $customForm->setAutoResSender($this->helperData->getCustomerNofSender());
        }

        $autoResFieldset->addField('auto_res_sender', 'select', [
            'name'               => 'auto_res_sender',
            'label'              => __('Sender'),
            'title'              => __('Sender'),
            'values'             => $this->emailIdentity->toOptionArray(),
            'container_id'       => 'row_form_auto_res_sender',
            'after_element_html' => $this->getUseConfigHtml('auto_res_sender', $autoResSenderChecked),
        ]);

        $autoResTemplateChecked =
            $customForm->getAutoResTemplate() === null
            || $customForm->getAutoResTemplate() === 'mp-use-config'
                ? 'checked' : '';
        if ($autoResSenderChecked) {
            $customForm->setAutoResTemplate($this->helperData->getCustomerNofEmailTemplate());
        }

        $autoResFieldset->addField('auto_res_template', 'select', [
            'name'               => 'auto_res_template',
            'label'              => __('Email Template'),
            'title'              => __('Email Template'),
            'values'             => $this->emailTemplate
                ->setPath('mp_custom_form/customer_notification_email_template')->toOptionArray(),
            'container_id'       => 'row_form_auto_res_template',
            'after_element_html' => $this->getUseConfigHtml('auto_res_template', $autoResTemplateChecked),
        ]);

        $autoResFieldset->addField('auto_res_email_address', 'select', [
            'name'   => 'auto_res_email_address',
            'label'  => __('Email Address Field'),
            'title'  => __('Email Address Field'),
            'values' => [$customForm->getAutoResEmailAddress() => ''],
            'note'   => __('Select field including email address of respondents')
        ]);

        /** @var RendererInterface $rendererBlock */
        $rendererBlock = $this->getLayout()->createBlock(EmailPlaning::class);

        $comment = __('Send After can be in text which is in relative formats.');
        $comment .= '<br/>';
        $comment .= __('Eg: Send After = +3 days');
        $comment .= '<br/>';
        $comment .= __('=> The email will be sent after 3 days.');
        $comment .= '<br/>';
        $comment .= __(
            'Please refer %1 for more custom duration.',
            '<a href="https://www.php.net/manual/en/datetime.formats.relative.php" target="_blank">here</a>'
        );

        $autoResFieldset->addField('email_planing', 'textarea', [
            'name'         => 'email_planing',
            'label'        => __('Email Planning'),
            'title'        => __('Email Planning'),
            'container_id' => 'row_form_email_planing',
            'note'         => $comment
        ])->setRenderer($rendererBlock);

        $autoResAttachedFilesChecked =
            $customForm->getAutoResAttachedFiles() === null
            || $customForm->getAutoResAttachedFiles() === '2'
                ? 'checked' : '';
        if ($autoResAttachedFilesChecked) {
            $customForm->setAutoResAttachedFiles($this->helperData->getCustomerAttachedFile());
        }

        $autoResFieldset->addField('auto_res_attached_files', 'select', [
            'name'               => 'auto_res_attached_files',
            'label'              => __('Attach Uploads File to Email'),
            'title'              => __('Attach Uploads File to Email'),
            'values'             => $this->yesno->toOptionArray(),
            'note'               => __('If yes, will attach uploaded files to the notification email sent to the customer.'),
            'after_element_html' => $this->getUseConfigHtml('auto_res_attached_files', $autoResAttachedFilesChecked, 2),
        ]);

        $form->addValues($customForm->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param $elemId
     * @param $checked
     * @param string $value
     *
     * @return string
     */
    protected function getUseConfigHtml($elemId, $checked, $value = 'mp-use-config')
    {
        return '<label class="mp-use-config-label" for="' . $elemId . '-use-config">' .
            '<input type="checkbox"
                class="mp-use-config"
                id="' . $elemId . '-use-config"
                name="form[' . $elemId . ']"
                value="' . $value . '" ' . $checked . '>' .
            __('Use Config') .
            '</label>';
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Email Notification');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
