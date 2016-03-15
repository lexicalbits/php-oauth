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
    protected $apiVersion = '1.0';

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUri(array $additionalParameters = [])
    {
        $parameters = array_merge(
            $additionalParameters,
            [
                'client_id'     => $this->credentials->getConsumerId(),
                'redirect_uri'  => $this->credentials->getCallbackUrl(),
                'response_type' => 'code',
            ]
        );

        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        $url->getQuery()->modify($parameters);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);
        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        }
        if(isset($data['error_description'])) {
            throw new TokenResponseException($data['error_description']);
        }
        if(isset($data['error'])) {
            throw new TokenResponseException($data['error']);
        }
        try {
            $token = $this->getAccessToken();
            if(!$token) $token = new StdOAuth2Token();
        } catch(TokenNotFoundException $tnfe) {
            $token = new StdOAuth2Token();
        }
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
        return $token;
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
                throw new InvalidRequestException($response->getReasonPhrase());
                break;
            case 401:
            case 403:
                throw new UnauthorizedRequestException($response->getReasonPhrase());
                break;
        }
    }
}
