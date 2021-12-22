<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\NetTerms\Model\Source;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class for discount calculation
 */
class DiscountCalculationValue extends Column
{
    const DISCOUNT_PERCENTAGE = 'discount_percentage';

    /**
     * Constructor
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct( // NOSONAR
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $percentage = "";
                if (isset($item[self::DISCOUNT_PERCENTAGE])) {
                    $percentage = $item[self::DISCOUNT_PERCENTAGE];
                }
                if ($item['discount_calculation_type'] == 1) {
                    $item[self::DISCOUNT_PERCENTAGE] = ($percentage / 100) . '%';
                } else {
                    $item[self::DISCOUNT_PERCENTAGE] = $percentage;
                }
            }
        }

        return $dataSource;
    }
}
