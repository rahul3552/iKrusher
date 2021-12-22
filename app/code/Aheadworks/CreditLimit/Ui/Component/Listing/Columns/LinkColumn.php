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
 * Class LinkColumn
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Columns
 */
class LinkColumn extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
        $urlEntityParamName = $this->getData('config/urlEntityParamName') ?? 'id';
        $entityFieldName = $this->getData('config/entityFieldName') ?? 'id';
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName . '_label'] = $item[$fieldName];
            $item[$fieldName . '_url'] = $this->context->getUrl(
                $viewUrlPath,
                [$urlEntityParamName => $item[$entityFieldName]]
            );
        }

        return $dataSource;
    }
}
