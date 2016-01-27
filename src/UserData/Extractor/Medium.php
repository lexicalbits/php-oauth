<?php
namespace OAuth\UserData\Extractor;

use OAuth\UserData\Arguments\FieldsValues;

/**
 * Class Medium
 *
 * @package OAuth\UserData\Extractor
 */
class Medium extends LazyExtractor
{

    /**
     * Request constants
     */
    const REQUEST_PROFILE = '/me';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            FieldsValues::construct(
                [
                    self::FIELD_UNIQUE_ID,
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
                        self::FIELD_UNIQUE_ID   => 'id',
                        self::FIELD_USERNAME    => 'username',
                        self::FIELD_FULL_NAME   => 'name',
                        self::FIELD_PROFILE_URL => 'url',
                        self::FIELD_IMAGE_URL   => 'imageUrl'
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
