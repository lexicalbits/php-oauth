<?php

namespace OAuth\OAuth1\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\OAuth1\Token\TokenInterface;

class Trello extends AbstractService
{

    protected $requestTokenEndpoint = 'https://trello.com/1/OAuthGetRequestToken';
    protected $authorizationEndpoint = 'https://trello.com/1/OAuthAuthorizeToken';
    protected $accessTokenEndpoint = 'https://trello.com/1/OAuthGetAccessToken';

    protected $baseApiUri = 'https://api.trello.com/1/';

    public function requestAccessToken($token, $verifier, $tokenSecret = null)
    {
        if (is_null($tokenSecret)) {
            /** @var TokenInterface $storedRequestToken */
            $storedRequestToken = $this->storage->retrieveAccessToken($this->service());
            $tokenSecret = $storedRequestToken->getAccessTokenSecret();
        }

        return parent::requestAccessToken($token, $verifier, $tokenSecret);
    }

    protected function parseRequestTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);
        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (!isset($data[ 'oauth_callback_confirmed' ]) || $data[ 'oauth_callback_confirmed' ] != 'true') {
            throw new TokenResponseException('Error in retrieving token.');
        }

        return $this->parseAccessTokenResponse($responseBody);
    }

    protected function parseAccessTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);
        if ($data === null || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data[ 'error' ])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data[ 'error' ] . '"');
        }

        $token = new StdOAuth1Token();
        $token->setRequestToken($data[ 'oauth_token' ]);
        $token->setRequestTokenSecret($data[ 'oauth_token_secret' ]);
        $token->setAccessToken($data[ 'oauth_token' ]);
        $token->setAccessTokenSecret($data[ 'oauth_token_secret' ]);
        $token->setEndOfLife(StdOAuth1Token::EOL_NEVER_EXPIRES);
        unset($data[ 'oauth_token' ], $data[ 'oauth_token_secret' ]);
        $token->setExtraParams($data);

        return $token;
    }
}

