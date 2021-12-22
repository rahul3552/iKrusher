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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor
 */
interface ProcessorInterface
{
    /**
     * Prepare post data for saving
     *
     * @param array $data
     * @return array
     */
    public function process($data);
}
