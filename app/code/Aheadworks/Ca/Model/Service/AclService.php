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
namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\AclManagementInterface;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\Acl\RootResource;
use Magento\Framework\Acl\AclResource\ProviderInterface;

/**
 * Class AclService
 * @package Aheadworks\Ca\Model\Service
 */
class AclService implements AclManagementInterface
{
    /**
     * @var RootResource
     */
    private $rootResource;

    /**
     * @var ProviderInterface
     */
    private $aclResourceProvider;

    /**
     * @param RootResource $rootResource
     * @param ProviderInterface $aclResourceProvider
     */
    public function __construct(
        RootResource $rootResource,
        ProviderInterface $aclResourceProvider
    ) {
        $this->rootResource = $rootResource;
        $this->aclResourceProvider = $aclResourceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootResourceId()
    {
        return $this->rootResource->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceKeys()
    {
        $resources = $this->getResourceStructure();

        return $this->mapResources($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceStructure()
    {
        return $this->aclResourceProvider->getAclResources();
    }

    /**
     * Map resources
     *
     * @param array $resources
     * @param array $output
     * @return array
     */
    private function mapResources($resources, $output = [])
    {
        foreach ($resources as $resource) {
            $output[] = $resource['id'];
            $output = $this->mapResources($resource['children'], $output);
        }
        return $output;
    }
}
