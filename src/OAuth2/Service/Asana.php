<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Http\Exception\InvalidRequestException;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Asana extends AbstractService
{
    protected $baseApiUri = 'https://app.asana.com/api/{apiVersion}/';
    protected $authorizationEndpoint = 'https://app.asana.com/-/oauth_authorize';
    protected $accessTokenEndpoint = 'https://app.asana.com/-/oauth_token';
    protected $authorizationMethod = self::AUTHORIZATION_METHOD_QUERY_STRING;
    protected $extraOAuthHeaders = ['Accept' => 'application/json'];
    protected $extraApiHeaders = ['Accept' => 'application/json'];
    protected $apiVersion = '1';

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);
        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        }
        $token = new StdOAuth2Token();
        $token->setAccessToken($data[ 'access_token' ]);
        if (isset($data[ 'expires_in' ])) {
            $token->setLifeTime($data[ 'expires_in' ]);
        }
        if (isset($data[ 'refresh_token' ])) {
            $token->setRefreshToken($data[ 'refresh_token' ]);
        }
        if (isset($data[ 'data' ])) {
            $token->setExtraParams($data['data']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertHttpRequestAuthorized($response)
    {
        switch($response->getStatusCode()) {
            case 400:
            case 404:
            case 500:
                throw new InvalidReqeustException($response->getReasonPhrase());
                break;
            case 401:
            case 403:
                throw new UnauthorizedRequestException($response->getReasonPhrase());
                break;
        }
    }
}
