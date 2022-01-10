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

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab\Renderer\CustomerName;
use Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab\Renderer\ViewResponse;
use Mageplaza\CustomForm\Model\Form as CustomFormModel;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\Grid\CollectionFactory as ResponsesCollectionFactory;

/**
 * Class ResponsesDetail
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab
 */
class ResponsesDetail extends Extended implements TabInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Store
     */
    private $systemStore;

    /**
     * @var ResponsesCollectionFactory
     */
    private $responsesCollectionFactory;

    /**
     * ResponsesDetail constructor.
     *
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param Registry $registry
     * @param Store $systemStore
     * @param ResponsesCollectionFactory $responsesCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        Registry $registry,
        Store $systemStore,
        ResponsesCollectionFactory $responsesCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->systemStore = $systemStore;
        $this->responsesCollectionFactory = $responsesCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('responses_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $customForm = $this->getCustomForm();
        $collection = $this->responsesCollectionFactory->create();
        $collection = $collection->addFieldToFilter('form_id', $customForm->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('response_id', [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'id',
            'type' => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('firstname', [
            'header' => __('Customer'),
            'name' => 'firstname',
            'index' => 'firstname',
            'renderer' => CustomerName::class,
        ]);
        $this->addColumn('ip_address', [
            'header' => __('IP Address'),
            'name' => 'ip_address',
            'index' => 'ip_address',
        ]);
        $this->addColumn('store_ids', [
            'header' => __('Store Views'),
            'index' => 'store_ids',
            'type' => 'options',
            'options' => $this->systemStore->getStoreOptionHash(true),
        ]);
        $this->addColumn('created_at', [
            'header' => __('Date'),
            'index' => 'created_at',
            'name' => 'created_at',
            'type' => 'datetime',
        ]);
        $this->addColumn('view', [
            'header' => __('Action'),
            'filter' => false,
            'sortable' => false,
            'name' => 'view',
            'index' => 'view',
            'renderer' => ViewResponse::class,
        ]);

        return $this;
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/responsesdetail', ['id' => $this->getCustomForm()->getId()]);
    }

    /**
     * @return CustomFormModel
     */
    public function getCustomForm()
    {
        return $this->registry->registry('mageplaza_custom_form_form');
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Responses Detail');
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

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mpcustomform/form/responsesdetail', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
