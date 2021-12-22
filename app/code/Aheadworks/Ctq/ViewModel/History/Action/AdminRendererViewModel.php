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
namespace Aheadworks\Ctq\ViewModel\History\Action;

use Aheadworks\Ctq\Model\Magento\ModuleUser\UserRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class AdminRendererViewModel
 * @package Aheadworks\Ctq\ViewModel\History\Action
 */
class AdminRendererViewModel implements ArgumentInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve admin name by id
     *
     * @param int $adminId
     * @return string
     */
    public function getAdminName($adminId)
    {
        try {
            $user = $this->userRepository->getById($adminId);
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = 'Undefined';
        }
        return $name;
    }
}
