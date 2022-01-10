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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Ui\Component\Listing\Columns\Logs;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\FileFactory;

/**
 * Class Group
 * @package Mageplaza\ProductAttachments\Ui\Component\Listing\Columns\Logs
 */
class Group extends Column
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Group constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FileFactory $fileFactory
     * @param Data $helperData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FileFactory $fileFactory,
        Data $helperData,
        array $components = [],
        array $data = []
    ) {
        $this->helperData  = $helperData;
        $this->fileFactory = $fileFactory;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $groups = $this->helperData->getGroups();
            $groups = $groups ? Data::jsonDecode($groups) : [];

            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['file_id']) {
                    $file = $this->fileFactory->create()->load($item['file_id']);
                    $groupValues                       = [];
                    $groupValue                        = $file->getGroup();
                    $item[$this->getData('name')]  = '';
                    foreach ($groups as $group) {
                        foreach ($group['value'] as $value) {
                            $groupValues[$value['value']] = $value['name'];
                        }
                    }

                    if (array_key_exists($groupValue, $groupValues)) {
                        $item[$this->getData('name')] = $groupValues[$groupValue];
                    }
                }
            }
        }

        return $dataSource;
    }
}
