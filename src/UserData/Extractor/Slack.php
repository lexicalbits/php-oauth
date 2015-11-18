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
 * @WARNING This doesn't actually work.  There's no "user" in the
 * Slack API, so there's no user information available...
 *
 * @package OAuth\UserData\Extractor
 */
class Slack extends LazyExtractor
{
    const REQUEST_INFO = '/users.info';

    public function __construct() {
        parent::__construct(
            FieldsValues::construct(
                [
                    self::FIELD_UNIQUE_ID,
                    self::FIELD_USERNAME,
                    self::FIELD_FULL_NAME,
                    self::FIELD_FIRST_NAME,
                    self::FIELD_LAST_NAME,
                    self::FIELD_EMAIL,
                    self::FIELD_IMAGE_URL
                ]
            ),
            self::getDefaultNormalizersMap()
                ->paths(
                    [
                        self::FIELD_UNIQUE_ID => 'user.id',
                        self::FIELD_USERNAME  => 'user.name',
                        self::FIELD_FULL_NAME => 'user.real_name',
                        self::FIELD_FIRST_NAME => 'user.first_name',
                        self::FIELD_LAST_NAME => 'user.last_name',
                        self::FIELD_EMAIL => 'user.email',
                        self::FIELD_IMAGE_URL => 'user.image_32'
                    ]
                )
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_INFO);
    }
} 
