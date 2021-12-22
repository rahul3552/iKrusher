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
namespace Aheadworks\OneStepCheckout\Ui\Component\Listing\Column;

use Aheadworks\OneStepCheckout\Model\Report\Source\Aggregation as AggregationSource;
use Aheadworks\OneStepCheckout\Ui\DataProvider\Aggregation;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Period
 * @package Aheadworks\OneStepCheckout\Ui\Component\Listing\Column
 */
class Period extends Column
{
    /**
     * @var Aggregation
     */
    private $aggregation;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Aggregation $aggregation
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Aggregation $aggregation,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->aggregation = $aggregation;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getName();

                $fromDate = new \DateTime($item['period_from']);
                $toDate = new \DateTime($item['period_to']);

                $aggregation = $this->aggregation->getAggregation();
                switch ($aggregation) {
                    case AggregationSource::DAY:
                        $item[$name] = $fromDate->format('M d, Y');
                        break;
                    case AggregationSource::WEEK:
                        $fromDateFormatted = $fromDate->format('M d, Y');
                        $toDateFormatted = $toDate->format('M d, Y');
                        $item[$name] = $fromDateFormatted . ' - ' . $toDateFormatted;
                        break;
                    case AggregationSource::MONTH:
                        $item[$name] = $fromDate->format('M Y');
                        break;
                    case AggregationSource::QUARTER:
                        $month = (integer)$fromDate->format('m');
                        $item[$name] = 'Q' . ceil($month / 3) . ' ' . $fromDate->format('Y');
                        break;
                    case AggregationSource::YEAR:
                        $item[$name] = $fromDate->format('Y');
                        break;
                }
            }
        }
        return $dataSource;
    }
}
