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
 * Class Bitly
 *
 * @package OAuth\UserData\Extractor
 */
class Bitly extends LazyExtractor
{

    /**
     * Request constants
     */
    const REQUEST_PROFILE = '/user/info';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            FieldsValues::construct(
                [
                    self::FIELD_USERNAME,
                    self::FIELD_FULL_NAME,
                    self::FIELD_FIRST_NAME,
                    self::FIELD_LAST_NAME,
                    self::FIELD_PROFILE_URL,
                    self::FIELD_IMAGE_URL,
                ]
            ),
            self::getDefaultNormalizersMap()
                ->pathContext('data')
                ->paths(
                    [
                        self::FIELD_USERNAME    => 'login',
                        self::FIELD_FULL_NAME   => 'full_name',
                        self::FIELD_LOCATION    => 'location',
                        self::FIELD_PROFILE_URL => 'profile_url',
                        self::FIELD_IMAGE_URL   => 'profile_image'
                    ]
                )
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }

    protected function firstNameNormalizer()
    {
        $fullName = $this->getField(self::FIELD_FULL_NAME);
        if ($fullName) {
            $names = explode(' ', $fullName);

            return $names[ 0 ];
        }

        return null;
    }

    protected function lastNameNormalizer()
    {
        $fullName = $this->getField(self::FIELD_FULL_NAME);
        if ($fullName) {
            $names = explode(' ', $fullName);

            return $names[ sizeof($names) - 1 ];
        }

        return null;
    }
}
