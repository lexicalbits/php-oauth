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
                    self::FIELD_IMAGE_URL,
                ]
            ),
            self::getDefaultNormalizersMap()
                ->pathContext('data.user')
                ->paths(
                    [
                        self::FIELD_UNIQUE_ID   => 'id',
                        self::FIELD_USERNAME    => 'username',
                        self::FIELD_FULL_NAME   => 'display_name',
                        self::FIELD_FIRST_NAME  => 'first_name',
                        self::FIELD_LAST_NAME   => 'last_name',
                        self::FIELD_IMAGE_URL   => 'profile_picture_url'
                    ]
                )
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }
}

