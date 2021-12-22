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
namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;

/**
 * Interface EmailProcessorInterface
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
interface EmailProcessorInterface
{
    /**
     * Process email
     *
     * @param CompanyInterface $company
     * @return EmailMetadataInterface[]
     */
    public function process($company);
}
