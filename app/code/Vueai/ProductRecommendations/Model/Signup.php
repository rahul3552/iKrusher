<?php
namespace Vueai\ProductRecommendations\Model;

use Magento\Framework\Model\AbstractModel;

class Signup extends AbstractModel
{

    /**
     * Define the resource model
     */
    protected function _construct()
    {
        $this->_init(\Vueai\ProductRecommendations\Model\ResourceModel\Signup::class);
    }
}
