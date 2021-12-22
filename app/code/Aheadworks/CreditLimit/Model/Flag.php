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
namespace Aheadworks\CreditLimit\Model;

use Magento\Framework\Flag as FrameworkFlag;

/**
 * Class Flag
 *
 * @package Aheadworks\CreditLimit\Model
 */
class Flag extends FrameworkFlag
{
    /**
     * Cron flag for last execution time of job runner
     */
    const AW_CL_JOB_RUNNER_LAST_EXEC_TIME = 'aw_credit_limit_job_runner_last_exec_time';

    /**
     * Setter for flag code
     *
     * @param string $code
     * @return $this
     */
    public function setAwClFlag($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
