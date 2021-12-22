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
 * Controller disables some types of cache
 */
class MassDisable extends \Magento\Backend\Controller\Adminhtml\Cache\MassDisable
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::toggling_cache_type';

    /**
     * Mass action for cache disabling
     *
     * @return Redirect
     */
    public function execute()
    {
        $this->disableCache();

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('adminhtml/*');
    }

    /**
     * Disable cache
     *
     * @return void
     */
    private function disableCache()
    {
        try {
            $types = $this->getRequest()->getParam('types');
            $updatedTypes = 0;
            if (!is_array($types)) {
                $types = [];
            }
            $this->_validateTypes($types);
            foreach ($types as $code) {
                $this->_cacheTypeList->cleanType($code);
                if ($this->_cacheState->isEnabled($code)) {
                    $this->_cacheState->setEnabled($code, false);
                    $updatedTypes++;
                }
            }
            if ($updatedTypes > 0) {
                $this->_cacheState->persist();
                $this->messageManager->addSuccessMessage(__("%1 cache type(s) disabled.", $updatedTypes));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while disabling cache.'));
        }
    }


}
