<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Service\Exception\InvalidAccessTypeException;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Google extends AbstractService
{

    //List of broad scopes available in Google's APIs
    const SCOPE_BOOKS = 'https://www.googleapis.com/auth/books';
    const SCOPE_CALENDAR = 'https://www.googleapis.com/auth/calendar';
    const SCOPE_DRIVE = 'https://www.googleapis.com/auth/drive';
    const SCOPE_MAIL = 'https://mail.google.com/';
    const SCOPE_CLOUD_PLATFORM = 'https://www.googleapis.com/auth/cloud-platform';
    const SCOPE_PROFILE = 'profile';
    const SCOPE_EMAIL = 'email';
    const SCOPE_DEVSTORAGE = 'https://www.googleapis.com/auth/devstorage.full_control';
    const SCOPE_YOUTUBE = 'https://www.googleapis.com/auth/youtube';
    const SCOPE_BLOGGER = 'https://www.googleapis.com/auth/blogger';
    const SCOPE_MONITORING = 'https://www.googleapis.com/auth/monitoring';
    const SCOPE_FUSION_TABLES = 'https://www.googleapis.com/auth/fusiontables';
    const SCOPE_ANALYTICS = 'https://www.googleapis.com/auth/analytics';
    const SCOPE_MAPS = 'https://www.googleapis.com/auth/mapsengine';
    const SCOPE_URL_SHORTENER = 'https://www.googleapis.com/auth/urlshortener';
    const SCOPE_BIGQUERY = 'https://www.googleapis.com/auth/bigquery';
    const SCOPE_CONTENT = 'https://www.googleapis.com/auth/content';
    const SCOPE_DOUBLECLICK = 'https://www.googleapis.com/auth/doubleclicksearch';
    const SCOPE_COMPUTE = 'https://www.googleapis.com/auth/compute';
    const SCOPE_GAMES = 'https://www.googleapis.com/auth/games';
    const SCOPE_TASKQUEUE = 'https://www.googleapis.com/auth/taskqueue';
    const SCOPE_TASKQUEUE_WORKER = 'https://www.googleapis.com/auth/taskqueue.consumer';
    const SCOPE_TASKS = 'https://www.googleapis.com/auth/tasks';
    const SCOPE_WEBMASTERS = 'https://www.googleapis.com/auth/webmasters';

    protected $accessType = 'offline';

    protected $authorizationEndpoint = 'https://accounts.google.com/o/oauth2/auth';
    protected $accessTokenEndpoint = 'https://accounts.google.com/o/oauth2/token';

    public function setAccessType($accessType)
    {
        if (!in_array($accessType, ['online', 'offline'], true)) {
            throw new InvalidAccessTypeException('Invalid accessType, expected either online or offline');
        }
        $this->accessType = $accessType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        $uri = parent::getAuthorizationEndpoint();
        $uri->getQuery()->modify(['access_type' => $this->accessType]);

        return $uri;
    }

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
        $token->setLifetime($data[ 'expires_in' ]);

        if (isset($data[ 'refresh_token' ])) {
            $token->setRefreshToken($data[ 'refresh_token' ]);
            unset($data[ 'refresh_token' ]);
        }

        unset($data[ 'access_token' ]);
        unset($data[ 'expires_in' ]);

        $token->setExtraParams($data);

        return $token;
    }
}
