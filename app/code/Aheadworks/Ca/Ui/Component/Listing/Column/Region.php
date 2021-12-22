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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Ui\Component\Listing\Column;

use Magento\Directory\Model\Region as RegionModel;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Region
 * @package Aheadworks\Ca\Ui\Component\Listing\Column
 */
class Region extends Column
{
    /**
     * @var RegionModel
     */
    private $region;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param RegionModel $region
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        RegionModel $region,
        array $components = [],
        array $data = []
    ) {
        $this->region = $region;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item['region_id'] = $this->prepareContent(
                $item['region_id'],
                $item['region']
            );
        }
        return $dataSource;
    }

    /**
     * Prepare content
     *
     * @param int $regionId
     * @param string $regionName
     * @return string
     */
    protected function prepareContent($regionId, $regionName)
    {
        return $regionId
            ? $this->region->load($regionId)->getName()
            : $regionName;
    }
}
