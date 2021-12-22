<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace I95DevConnect\EgeClaue\Controller\Adminhtml\Cache;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

/**
 * Controller enables some types of cache
 */
class MassEnable extends \Magento\Backend\Controller\Adminhtml\Cache\MassEnable
{

    /**
     * Mass action for cache enabling
     *
     * @return Redirect
     */
    public function execute()
    {
        $this->enableCache();

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('adminhtml/*');
    }

    /**
     * Enable cache
     *
     * @return void
     */
    private function enableCache()
    {
        try {
            $types = $this->getRequest()->getParam('types');
            $updatedTypes = 0;
            if (!is_array($types)) {
                $types = [];
            }
            $this->_validateTypes($types);
            foreach ($types as $code) {
                if (!$this->_cacheState->isEnabled($code)) {
                    $this->_cacheState->setEnabled($code, true);
                    $updatedTypes++;
                }
            }
            if ($updatedTypes > 0) {
                $this->_cacheState->persist();
                $this->messageManager->addSuccessMessage(__("%1 cache type(s) enabled.", $updatedTypes));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while enabling cache.'));
        }
    }
}
