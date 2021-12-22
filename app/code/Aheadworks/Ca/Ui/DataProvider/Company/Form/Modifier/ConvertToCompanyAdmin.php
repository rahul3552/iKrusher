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
namespace Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class ConvertToCompanyAdmin
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier
 */
class ConvertToCompanyAdmin implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $data['isEnabledConverter'] = !(isset($data['id']) && $data['id'] != '');
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
