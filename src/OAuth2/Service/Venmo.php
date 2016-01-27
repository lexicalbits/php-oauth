<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Venmo extends AbstractService
{

    /**
     * Defined scopes
     *
     * @link http://instagram.com/developer/authentication/#scope
     */
    const SCOPE_MAKE_PAYMENTS = 'make_payments';
    const SCOPE_PAYMENT_HISTORY = 'access_payment_history';
    const SCOPE_FEED = 'access_feed';
    const SCOPE_PROFILE = 'access_profile';
    const SCOPE_EMAIL = 'access_email';
    const SCOPE_PHONE = 'access_phone';
    const SCOPE_BALANCE = 'access_balance';
    const SCOPE_FRIENDS = 'access_friends';

    protected $baseApiUri = 'https://api.venmo.com/{apiVersion}/';
    protected $authorizationEndpoint = 'https://api.venmo.com/{apiVersion}/oauth/authorize';
    protected $accessTokenEndpoint = 'https://api.venmo.com/{apiVersion}/oauth/access_token';
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
        $token->setLifeTime($data[ 'expires_in' ]);
        $token->setRefreshToken($data['refresh_token']);
        $extraData = $data['user'];
        $extraData['balance'] = $data['balance'];

        $token->setExtraParams($extraData);

        return $token;
    }
}
