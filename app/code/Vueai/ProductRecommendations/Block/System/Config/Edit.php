<?php

namespace Vueai\ProductRecommendations\Block\System\Config;

use \Magento\Backend\Block\Template\Context;
use \Magento\Backend\Block\Widget;
use \Magento\Config\Model\Config\Structure;
use \Magento\Framework\Serialize\Serializer\Json;

class Edit extends Widget
{
    const DEFAULT_SECTION_BLOCK = \Magento\Config\Block\System\Config\Form::class;

    /**
     * @var Structure
     */
    private $configStructure;

    /**
     * @var Json|null
     */
    private $jsonSerializer;

    /**
     * Edit constructor.
     * @param Context $context
     * @param Structure $configStructure
     * @param array $data
     * @param Json|null $jsonSerializer
     */
    public function __construct(
        Context $context,
        Structure $configStructure,
        array $data = [],
        Json $jsonSerializer = null
    ) {
        $this->configStructure = $configStructure;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section = $this->configStructure->getElement($this->getRequest()->getParam('section'));

        $this->formBlockName = $section->getFrontendModel();
        if (empty($this->formBlockName)) {
            $this->formBlockName = self::DEFAULT_SECTION_BLOCK;
        }
        $this->setTitle($section->getLabel());
        $this->setHeaderCss($section->getHeaderCss());

        $this->getToolbar()->addChild(
            'system_config_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'id'     => 'configuration',
                'label'  => __('Configuration'),
                'class'  => 'system primary',
                'onclick'=>
                   'setLocation(\'' . $this->getUrl('adminhtml/system_config/edit/section/oauth_details') . '\')'
            ]
        );
        $block = $this->getLayout()->createBlock($this->formBlockName);
        $this->setChild('form', $block);
        return parent::_prepareLayout();
    }
}
