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

/**
 * Class GitHub
 *
 * @package OAuth\UserData\Extractor
 */
class Asana extends LazyExtractor
{

    const REQUEST_PROFILE = '/users/me';

    public function __construct()
    {
        parent::__construct(
            FieldsValues::construct(
                [
                    self::FIELD_UNIQUE_ID,
                    self::FIELD_USERNAME,
                    self::FIELD_EMAIL,
                    self::FIELD_IMAGE_URL
                ]
            ),
            self::getDefaultNormalizersMap()
                ->pathContext('data')
                ->paths(
                    [
                        self::FIELD_UNIQUE_ID   => 'id',
                        self::FIELD_USERNAME    => 'name',
                        self::FIELD_EMAIL       => 'email'
                    ]
                ),
            self::getDefaultLoadersMap()
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }

    protected function imageUrlNormalizer($images)
    {
        if(isset($images['image128x128'])) {
            return $images['image128x128'];
        }
        return null;
    }
}

