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
namespace Aheadworks\Ctq\Model;

use Magento\Framework\Flag as FrameworkFlag;

/**
 * Class Flag
 *
 * @package Aheadworks\Ctq\Model
 */
class Flag extends FrameworkFlag
{
    /**#@+
     * Constants for aw ctq cron flags
     */
    const AW_CTQ_CHECK_QUOTE_EXPIRATION_LAST_EXEC_TIME = 'aw_ctq_check_quote_expiration_last_exec_time';
    const AW_CTQ_PROCESS_EXPIRATION_REMINDER_LAST_EXEC_TIME = 'aw_ctq_process_expiration_reminder_last_exec_time';
    /**#@-*/

    /**
     * Setter for flag code
     *
     * @param string $code
     * @return $this
     * @codeCoverageIgnore
     */
    public function setCtqFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
