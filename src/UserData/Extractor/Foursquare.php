<?php

/*
 * This file is part of the php-oauth package <https://github.com/logical-and/php-oauth>.
 *
 * (c) Oryzone, developed by Luciano Mammino <lmammino@oryzone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Arguments\FieldsValues;
use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

/**
 * Class Facebook
 *
 * @package OAuth\UserData\Extractor
 */
class Foursquare extends LazyExtractor
{

    /**
     * Request contants
     */
    const REQUEST_PROFILE = '/users/self';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            FieldsValues::construct(
                [
                    self::FIELD_UNIQUE_ID,
                    self::FIELD_FIRST_NAME,
                    self::FIELD_LAST_NAME,
                    self::FIELD_DESCRIPTION,
                    self::FIELD_LOCATION,
                    self::FIELD_IMAGE_URL
                ]
            ),
            self::getDefaultNormalizersMap()
                ->pathContext('response.user')
                ->paths(
                    [
                        self::FIELD_UNIQUE_ID   => 'id',
                        self::FIELD_FIRST_NAME  => 'firstName',
                        self::FIELD_LAST_NAME   => 'lastName',
                        self::FIELD_DESCRIPTION => 'bio',
                        self::FIELD_LOCATION    => 'homeCity',
                        self::FIELD_PROFILE_URL => 'link',
                    ]
                )
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }
    protected function imageUrlNormalizer($response) {
        if(isset($response['response'])
            && isset($response['response']['user'])
            && isset($response['response']['user']['photo'])
        ) {
            $data = $response['response']['user']['photo'];
            return implode('', [$data['prefix'], 'original', $data['suffix']]);
        }
        return null;
    }
}

