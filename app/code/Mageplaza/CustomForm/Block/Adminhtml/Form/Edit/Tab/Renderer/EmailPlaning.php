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

namespace Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab\Renderer;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Config\Model\Config\Source\Email\Template as EmailTemplate;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Phrase;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Config\Source\Active;

/**
 * Class EmailPlaning
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab\Renderer
 */
class EmailPlaning extends AbstractFieldArray
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_CustomForm::form/email-planing.phtml';

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @var CollectionFactory
     */
    protected $templatesFactory;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var EmailTemplate
     */
    protected $emailTemplate;

    /**
     * @var Active
     */
    protected $active;

    /**
     * EmailPlaning constructor.
     *
     * @param Context $context
     * @param Factory $elementFactory
     * @param CollectionFactory $templatesFactory
     * @param Yesno $yesno
     * @param EmailTemplate $emailTemplate
     * @param Active $active
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        CollectionFactory $templatesFactory,
        Yesno $yesno,
        EmailTemplate $emailTemplate,
        Active $active,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->templatesFactory = $templatesFactory;
        $this->yesno = $yesno;
        $this->emailTemplate = $emailTemplate;
        $this->active = $active;

        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    public function _construct()
    {
        $this->addColumn('name', ['label' => __('Name')]);
        $this->addColumn('status', ['label' => __('Status')]);
        $this->addColumn('template', ['label' => __('Template')]);
        $this->addColumn('send_after', ['label' => __('Send After')]);

        $this->_addAfter = false;

        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     *
     * @return mixed|string
     * @throws Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (!empty($this->_columns[$columnName])) {
            switch ($columnName) {
                case 'template':
                    $options = $this->getEmailTemplateOption();
                    break;
                case 'status':
                    $options = $this->active->toOptionArray();
                    break;
                default:
                    $options = '';
                    break;
            }
            if ($options) {
                $element = $this->elementFactory->create('select');
                $element->setForm($this)
                    ->setName($this->_getCellInputElementName($columnName))
                    ->setHtmlId($this->_getCellInputElementId('<%- _id %>', $columnName))
                    ->setValues($options);

                return str_replace("\n", '', $element->getElementHtml());
            }
        }

        if ($columnName === 'name') {
            $this->_columns[$columnName]['class'] = 'required-entry _required';
        }

        return parent::renderCellTemplate($columnName);
    }

    /**
     * Generate list of email templates
     *
     * @return array
     */
    private function getEmailTemplateOption()
    {
        return $this->emailTemplate->setPath('mp_custom_form/customer_notification_email_template')->toOptionArray();
    }

    /**
     * Get Button Label
     *
     * @return Phrase
     */
    public function getAddButtonLabel()
    {
        return __('Add');
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        // Disable element if value is inherited from other scope. Flag has to be set before the value is rendered.
        if ($isCheckboxRequired && (int)$element->getInherit() === 1) {
            $element->setDisabled(true);
        }

        $html = $this->_renderValue($element);

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * @return array
     */
    public function getArrayRows()
    {
        if (is_string($this->getElement())) {
            $this->setElement(Data::jsonDecode($this->getElement()));
        }

        return parent::getArrayRows();
    }
}
