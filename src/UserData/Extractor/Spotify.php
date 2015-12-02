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

/**
 *
 * Note that there is an email field available,
 * but it requires an additional scope.
 *
 * @package OAuth\UserData\Extractor
 */
class Spotify extends LazyExtractor
{
    const REQUEST_INFO = '/me';

    public function __construct() {
        parent::__construct(
            FieldsValues::construct(
                [
                    self::FIELD_UNIQUE_ID,
                    self::FIELD_FULL_NAME,
                    self::FIELD_PROFILE_URL
                ]
            ),
            self::getDefaultNormalizersMap()
                ->paths(
                    [
                        self::FIELD_UNIQUE_ID => 'id',
                        self::FIELD_FULL_NAME => 'display_name',
                        self::FIELD_PROFILE_URL => 'uri'
                    ]
                )
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_INFO);
    }
} 

