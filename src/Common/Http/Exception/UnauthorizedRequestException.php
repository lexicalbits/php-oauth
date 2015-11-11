<?php

namespace OAuth\Common\Http\Exception;

use OAuth\Common\Exception\Exception;

/**
 * Request thrown when a service rejects a request due to access restrictions
 */
class UnauthorizedRequestException extends Exception
{
    public function __construct($message, $code=401)
    {
        $this->code = $code;
        parent::__construct($message);
    }
}
