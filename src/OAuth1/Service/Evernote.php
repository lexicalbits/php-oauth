<?php

namespace OAuth\OAuth1\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth1\Token\StdOAuth1Token;

class Evernote extends AbstractService
{
    const BASE_SANDBOX = 'https://sandbox.evernote.com';
    const BASE_PRODUCTION = 'https://www.evernote.com';
    const BASE_CHINA = 'https://app.yinxiang.com';

    protected $baseApiUri = 'https://api.tumblr.com/v2/';
    protected $requestTokenEndpoint = 'https://sandbox.evernote.com/oauth';
    protected $authorizationEndpoint = 'https://sandbox.evernote.com/OAuth.action';
    protected $accessTokenEndpoint = 'https://sandbox.evernote.com/oauth';

    public function getBaseApiUri($clone = true) {
        //TODO Eventually this should use a custom setting to get the correct URL
        return self::BASE_SANDBOX;
    }

    public function requestAccessToken($token, $verifier, $tokenSecret = null)
    {
        if (is_null($tokenSecret)) {
            /** @var TokenInterface $storedRequestToken */
            $storedRequestToken = $this->storage->retrieveAccessToken($this->service());
            $tokenSecret = $storedRequestToken->getAccessTokenSecret();
        }

        return parent::requestAccessToken($token, $verifier, $tokenSecret);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseRequestTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (!isset($data[ 'oauth_callback_confirmed' ]) || $data[ 'oauth_callback_confirmed' ] !== 'true') {
            throw new TokenResponseException('Error in retrieving token.');
        }

        return $this->parseAccessTokenResponse($responseBody);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (null === $data || !is_array($data)) {
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

