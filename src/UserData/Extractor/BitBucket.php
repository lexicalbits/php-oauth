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
                    self::FIELD_LAST_NAME
                ]
            ),
            self::getDefaultNormalizersMap()
                ->paths(
                    [
                        self::FIELD_USERNAME    => 'username',
                        self::FIELD_FIRST_NAME  => 'first_name',
                        self::FIELD_LAST_NAME   => 'last_name',
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

    protected function emailNormalizer($emails)
    {
        $email = $this->getEmailObject($emails);

        return (is_array($email)) ? $email[ 'email' ] : $email;
    }

    protected function verifiedEmailNormalizer($emails)
    {
        $email = $this->getEmailObject($emails);

        return $email[ 'verified' ];
    }

    /**
     * Get the right email address from the one's the user provides.
     *
     * @param array $emails The array of email array objects provided by BitBucket.
     *
     * @return array The email array object.
     */
    private function getEmailObject($emails)
    {
        // Try to find an email address which is primary and verified.
        foreach ($emails as $email) {
            if (!empty($email[ 'primary' ]) && !empty($email[ 'verified' ])) {
                return $email;
            }
        }

        // Try to find an email address which is primary.
        foreach ($emails as $email) {
            if (!empty($email[ 'primary' ])) {
                return $email;
            }
        }

        return $emails[ 0 ];
    }
}

