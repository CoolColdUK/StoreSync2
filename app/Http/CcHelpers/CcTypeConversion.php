<?php
namespace App\Http\CcHelpers;

class CcTypeConversion
{
    //////////////////////////bool//////////////////////

    /**
     * Convert string to bool
     *
     * @var $input input string
     * @return bool
     */
    public static function BoolString2Bool(string $input)
    {
        return $input == "true";
    }

    /**
     * Convert string to bool
     *
     * @var $input input int
     * @return bool
     */
    public static function BoolInt2Bool($input)
    {
        return ((int) $input) == 1;
    }

    /**
     * Convert bool to string
     *
     * @var $input input bool
     * @return string
     */
    public static function StringBool2String(bool $input)
    {
        return $input ? "true" : "false:";
    }

    /**
     * Convert bool to int
     *
     * @var $input input bool
     * @return int
     */
    public static function StringBool2Int(bool $input)
    {
        return $input ? 1 : 0;
    }

    //////////////////////////encode string//////////////////////

    /**
     * Encode string and restrict character length
     *
     * @return string
     * @var $input input bool
     * @var $max_char truncate string to maximum char
     */
    public static function EncodeString2String(string $input, int $max_char = 0)
    { //encode is string for use internally after processed by
        if ($max_char > 0) {
            $input = substr($input, 0, $max_char);
        }
        return htmlentities($input, ENT_QUOTES);
    }

    //////////////////////////decode string//////////////////////

    /**
     * Decode string and restrict character length
     *
     * @return string
     * @var $input input bool
     * @var $max_char truncate string to maximum char
     */
    public static function DecodeString2String(string $input, int $max_char = 0)
    { //encode is string for use internally after processed by
        if ($max_char > 0) {
            return substr(html_entity_decode($input, ENT_QUOTES), 0, $max_char);
        }
        return html_entity_decode($input, ENT_QUOTES);
    }

    //////////////////////////array//////////////////////
    public static function StringArray2String(array $input)
    {

    }
}
