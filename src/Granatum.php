<?php

namespace GabrielKoerich\Granatum;

class Granatum
{
    /**
     * The API token.
     *
     * @var string
     */
    public static $token;

    /**
     * The API base endpoint.
     *
     * @var string
     */
    public static $base = 'https://api.granatum.com.br/v1';

    /**
     * Set the API token.
     */
    public static function setApiToken(string $token)
    {
        self::$token = $token;
    }
}
