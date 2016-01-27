<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Medium extends AbstractService
{

    /**
     * Defined scopes
     *
     * @link http://instagram.com/developer/authentication/#scope
     */
    const SCOPE_BASIC = 'basicProfile';
    const SCOPE_LIST_PUBLICATIONS = 'listPublications';
    const SCOPE_PUBLISH_POST = 'publishPost';
    const SCOPE_UPLOAD_IMAGE = 'uploadImage';

    protected $baseApiUri = 'https://api.medium.com/{apiVersion}/';
    protected $authorizationEndpoint = 'https://medium.com/m/oauth/authorize';
    protected $accessTokenEndpoint = 'https://api.medium.com/{apiVersion}/v1/tokens';
    protected $authorizationMethod = self::AUTHORIZATION_METHOD_QUERY_STRING;
    protected $apiVersion = 'v1';

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data[ 'error' ])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data[ 'error' ] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data[ 'access_token' ]);
        unset($data[ 'access_token' ]);

        if(isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }
        if(isset($data['expires_at'])) {
            $token->setEndOfLife($data['expires_at']);
            unset($data['expires_at']);
        }

        $token->setExtraParams($data);

        return $token;
    }
}
