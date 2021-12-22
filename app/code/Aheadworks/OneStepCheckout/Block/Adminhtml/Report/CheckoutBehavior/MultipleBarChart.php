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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior;

use Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior\AddressAttributes\MetaProviderFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class MultipleBarChart
 *
 * @method string getTitle()
 * @method string getDescription()
 * @method string getNote()
 * @method string getScope()
 * @method string getMetaProviderClassName()
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior
 */
class MultipleBarChart extends Template
{
    /**
     * inheritdoc
     */
    protected $_template = 'Aheadworks_OneStepCheckout::report/checkout_behavior/multiple_bar_chart.phtml';

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var MetaProviderFactory
     */
    private $metaProviderFactory;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @param Context $context
     * @param Provider $provider
     * @param MetaProviderFactory $metaProviderFactory
     * @param Formatter $formatter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Provider $provider,
        MetaProviderFactory $metaProviderFactory,
        Formatter $formatter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->provider = $provider;
        $this->metaProviderFactory = $metaProviderFactory;
        $this->formatter = $formatter;
    }

    /**
     * Get charts data
     *
     * @return array
     */
    public function getChartsData()
    {
        $chartData = [];
        $metaProvider = $this->metaProviderFactory->create($this->getMetaProviderClassName());
        // todo: take into account current scope
        $metadata = $metaProvider->getMetadata();
        foreach ($metadata as $meta) {
            $criteria = [['field_name' => $meta['code'], 'scope' => $this->getScope()]];
            $items = $this->provider->findItems($criteria);
            $item = count($items) > 0
                    ? ['value' => (int)$items[0]['completed'], 'percentage' => (float)$items[0]['completed_percent']]
                    : ['value' => 0, 'percentage' => 0];
            foreach ($meta as $key => $value) {
                $item[$key] = $value;
            }
            $chartData[] = $item;
        }
        return $chartData;
    }

    /**
     * Format percents
     *
     * @param float $value
     * @return string
     */
    public function formatPercents($value)
    {
        return $this->formatter->formatPercents($value);
    }
}
