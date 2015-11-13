<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Slack extends AbstractService
{

    const SCOPE_CHANNELS_WRITE = 'channels:write';
    const SCOPE_CHANNELS_HISTORY = 'channels:history';
    const SCOPE_CHANNELS_READ = 'channels:read';
    const SCOPE_CHAT_WRITE = 'chat:write';
    const SCOPE_CHAT_WRITE_BOT = 'chat:write:bot';
    const SCOPE_CHAT_WRITE_USER = 'chat:write:user';
    const SCOPE_EMOJI_READ = 'emoji:read';
    const SCOPE_FILES_WRITE_USER = 'files:write:user';
    const SCOPE_FILES_READ = 'files:read';
    const SCOPE_GROUPS_WRITE = 'groups:write';
    const SCOPE_GROUPS_HISTORY = 'groups:history';
    const SCOPE_GROUPS_READ = 'groups:read';
    const SCOPE_IM_WRITE = 'im:write';
    const SCOPE_IM_HISTORY = 'im:history';
    const SCOPE_IM_READ = 'im:read';
    const SCOPE_MPIM_WRITE = 'mpim:write';
    const SCOPE_MPIM_HISTORY = 'mpim:history';
    const SCOPE_MPIM_READ = 'mpim:read';
    const SCOPE_PINS_WRITE = 'pins:write';
    const SCOPE_PINS_READ = 'pins:read';
    const SCOPE_REACTIONS_WRITE = 'reactions:write';
    const SCOPE_REACTIONS_READ = 'reactions:read';
    const SCOPE_SEARCH_READ = 'search:read';
    const SCOPE_STARS_WRITE = 'stars:write';
    const SCOPE_STARS_READ = 'stars:read';
    const SCOPE_TEAM_READ = 'team:read';
    const SCOPE_USERS_READ = 'users:read';
    const SCOPE_USERS_WRITE = 'users:write';

    
    protected $authorizationMethod = self::AUTHORIZATION_METHOD_QUERY_STRING_V4;
    protected $baseApiUri = 'https://slack.com/api/';
    protected $authorizationEndpoint = 'https://slack.com/oauth/authorize';
    protected $accessTokenEndpoint = 'https://slack.com/api/oauth.access';
    protected $apiVersion = '';

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($_GET[ 'error' ])) {
            throw new TokenResponseException('Error in retrieving token: "' . $_GET[ 'error' ] . '"');
        } elseif (empty($data['ok'])) {
            $msg = (isset($data['error'])) ? $data['error'] : 'Unknown error';
            throw new TokenResponseException($msg);
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data[ 'access_token' ]);
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset($data[ 'access_token' ]);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function requestJSON($uri, array $body = [], $method = 'GET', array $extraHeaders = [])
        {
        $data = parent::requestJSON($uri, $body, $method, $extraHeaders);
        if(empty($data['ok'])) {
            $error = isset($data['error']) ? $data['error'] : 'Unknown error';
            throw new \Exception('Invalid slack request: '.$error);
        }
        unset($data['ok']);
        return $data;
    }
}
