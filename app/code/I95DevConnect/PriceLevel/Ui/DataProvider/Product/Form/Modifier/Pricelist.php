<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form;
use Magento\Framework\UrlInterface;

/**
 * Class Pricelist Renderer
 */
class Pricelist extends AbstractModifier
{
    const GROUP_PRICELIST = 'pricelist';
    const GROUP_CONTENT = 'downloadable';
    const DATA_SCOPE_PRICELIST = 'grouped';
    const SORT_ORDER = 200;
    const LINK_TYPE = 'associated';
    const PRICELIST_LISTING = 'pricelist_listing';

    /**
     * @var LocatorInterface
     */
    public $locator;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /*
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $helper;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\PriceLevel\Helper\Data $helper
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\PriceLevel\Helper\Data $helper
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->dataHelper = $dataHelper;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId()) {
            return $meta;
        }

        /**@updatedBy kavya.k, adding if condition to check pricelevel module is enabled **/
        if ($this->helper->isEnabled() == 1) {
            $meta[static::GROUP_PRICELIST] = [
                'children' => [
                    self::PRICELIST_LISTING => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'autoRender' => true,
                                    'componentType' => 'insertListing',
                                    'dataScope' => self::PRICELIST_LISTING,
                                    'externalProvider' => 'pricelist_listing.pricelist_listing_data_source',
                                    'selectionsProvider' => 'pricelist_listing.pricelist_listing.product_columns.ids',
                                    'ns' => self::PRICELIST_LISTING,
                                    'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                    'realTimeLink' => false,
                                    'behaviourType' => 'simple',
                                    'externalFilterMode' => true,
                                    'imports' => [
                                        'productId' => '${ $.provider }:data.product.current_product_id'
                                    ],
                                    'exports' => [
                                        'productId' => '${ $.externalProvider }:params.current_product_id'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Price List'),
                            'collapsible' => true,
                            'opened' => false,
                            'componentType' => Form\Fieldset::NAME,
                            'sortOrder' =>
                            $this->getNextGroupSortOrder(
                                $meta,
                                static::GROUP_CONTENT,
                                static::SORT_ORDER
                            ),
                        ],
                    ],
                ],
            ];
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;

        return $data;
    }
}
