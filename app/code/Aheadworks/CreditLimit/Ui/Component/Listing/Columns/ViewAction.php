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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Columns
 */
class ViewAction extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $entityFieldName = $this->getData('config/entityFieldName') ?? 'id';
                if (isset($item[$entityFieldName])) {
                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?? 'id';
                    $params = [
                        $urlEntityParamName => $item[$entityFieldName]
                    ];
                    $additionalParam = $this->getData('config/additionalParamName');
                    if ($additionalParam) {
                        $params[$additionalParam] = $this->getData('config/additionalParamValue');
                    }

                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->context->getUrl($viewUrlPath, $params),
                            'label' => __('Edit')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
