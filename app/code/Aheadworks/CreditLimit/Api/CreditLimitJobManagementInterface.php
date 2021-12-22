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
namespace Aheadworks\CreditLimit\Api;

/**
 * Interface CreditLimitJobManagementInterface
 * @api
 */
interface CreditLimitJobManagementInterface
{
    /**
     * Add new job
     *
     * @param \Aheadworks\CreditLimit\Api\Data\JobInterface $job
     * @return bool
     * @throws \Exception
     */
    public function addNewJob(\Aheadworks\CreditLimit\Api\Data\JobInterface $job);

    /**
     * Run all ready to process jobs
     *
     * @return bool
     */
    public function runAllJobs();
}
