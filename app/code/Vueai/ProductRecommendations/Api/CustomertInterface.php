<?php
namespace Vueai\ProductRecommendations\Api;

interface CustomertInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $domainName
     * @param string $email
     * @param string $status
     * @param string $embededCode
     * @param string $apiUrl
     * @return string Greeting message with users name.
     */
    public function getVueaiData($domainName, $email, $status, $embededCode, $apiUrl);
}
