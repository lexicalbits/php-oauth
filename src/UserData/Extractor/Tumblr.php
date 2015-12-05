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
 * Class Tumblr
 *
 * @package OAuth\UserData\Extractor
 */
class Tumblr extends LazyExtractor
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
                ]
            ),
            self::getDefaultNormalizersMap()
                ->pathContext('response.user')
                ->paths(
                    [
                        self::FIELD_USERNAME    => 'name',
                    ]
                )
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }
}

