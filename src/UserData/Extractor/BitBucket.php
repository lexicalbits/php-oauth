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
 * Class BitBucket
 *
 * @package OAuth\UserData\Extractor
 */
class BitBucket extends LazyExtractor
{

    const REQUEST_PROFILE = '/user';

    public function __construct()
    {
        parent::__construct(
            FieldsValues::construct(
                [
                    self::FIELD_USERNAME,
                    self::FIELD_FIRST_NAME,
                    self::FIELD_LAST_NAME,
                    self::FIELD_IMAGE_URL,
                ]
            ),
            self::getDefaultNormalizersMap()
                ->pathContext('user')
                ->paths(
                    [
                        self::FIELD_USERNAME    => 'username',
                        self::FIELD_FIRST_NAME  => 'first_name',
                        self::FIELD_LAST_NAME   => 'last_name',
                        self::FIELD_IMAGE_URL   => 'avatar',
                    ]
                ),
            self::getDefaultLoadersMap()
                ->loader('email')->readdFields([self::FIELD_EMAIL, self::FIELD_VERIFIED_EMAIL])
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }
}

