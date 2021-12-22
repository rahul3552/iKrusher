<?php
/**
 * @author i95Dev <arushi bansal>
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Ui\Component\Customer\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class to add Origin
 */
class Origin extends Column
{

    public $helperData;

    /**
     * Origin constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $helperData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \I95DevConnect\MessageQueue\Helper\Data $helperData,
        array $components = [],
        array $data = []
    ) {

        $this->helperData = $helperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheridoc
     */
    public function prepare()
    {
        if (!$this->helperData->isEnabled()) {

            $this->setData(
                'config',
                array_replace_recursive(
                    ['componentDisabled' =>true],
                    (array)$this->getData('config')
                )
            );
        }

        parent::prepare();
    }
}
