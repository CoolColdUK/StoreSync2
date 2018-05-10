<?php
namespace App\Http\CcHelpers;

class CcArray
{

    /**
     * Remove all key and value where value is null, recursively
     *
     * @return array
     * @var $item array to be processed on
     */
    public static function RemoveNull($item)
    {
        if (!is_array($item)) {
            return $item;
        }

        return collect($item)
            ->reject(function ($item) {
                return is_null($item);
            })
            ->flatMap(function ($item, $key) {

                return is_numeric($key)
                ? [self::RemoveNull($item)]
                : [$key => self::RemoveNull($item)];
            })
            ->toArray();
    }

    /**
     * Convert array to GET query string in url using key=value
     *
     * @return array
     * @var $param_arr array to convert
     */
    public static function ToQueryStr($param_arr)
    {
        if ($param_arr == null) {return "";}
        return implode('&', array_map(
            function ($v, $k) {return sprintf("%s=%s", $k, is_array($v) ? join(",", $v) : $v);},
            $param_arr,
            array_keys($param_arr)
        ));
    }

    /**
     * Split string to array with given key and counter
     *
     * @return array
     * @var string $str string to be split
     * @var string $key key to use
     * @var string $delimiter delimiter for explode
     * @var bool $trim delimiter for explode
     */
    public static function SplitStrToArray(string $str, string $key,
        string $delimiter = ",", bool $trim = false) {
        if (strlen($str) == 0) {return array();}
        $tmp = explode($delimiter, $str);

        for ($i = 0; $i < sizeof($tmp); $i++) {
            $rtn[$key . ($i + 1)] = $trim ? trim($tmp[$i]) : $tmp[$i];
        }
        return $rtn;
    }

    /**
     * Join array to string with given key and counter
     *
     * @return string
     * @var array $arr array to join
     * @var string $key key to use
     * @var string $delimiter delimiter for explode
     * @var bool $remove_empty remove empty element
     */
    public static function JoinArrToStr(array $arr,string $key, 
    string $delimiter = ",", bool $remove_empty = false)
    {
        $rtn = "";
        for ($i = 0; $i < sizeof($arr); $i++) {
            if (!isset($arr[$key . ($i + 1)])) {
                break;
            }

            //skip if empty array
            if ($remove_empty && strlen($arr[$key . ($i + 1)]) == 0) {continue;}

            //add to string
            $rtn .= ($i == 0 ? "" : $delimiter) . $arr[$key . ($i + 1)];
        }
        return $rtn;
    }

    /**
     * Convert column array to row array
     *
     * @return array
     * @var $arr array to be processed on, integer index
     */
    public static function TransposeArray(array $arr)
    {
        $rtn = [];
        $row = 0;
        while (true) {
            $merge = [];

            for ($i = 0; $i < sizeof($arr); $i++) {
                if (!isset($arr[$i][$row])) {return $rtn;}
                $merge[] = $arr[$i][$row];
            }

            //convert to row array
            $rtn[] = $merge;
            $row++;
        }
        return $rtn;
    }
    /**
     * Remove from input array keys that is not on whitelist
     *
     * @return array
     * @var array $input_arr input array
     * @var array $whitelist white list for keys to keep
     */
    public static function WhitelistKey(array $input_arr, array $whitelist)
    {
        return array_intersect_key($input_arr, array_flip($whitelist));
    }

    /**
     * Remove from input array keys that is on blacklist
     *
     * @return array
     * @var array $input_arr input array
     * @var array $blacklist black list for keys to keep
     */
    public static function BlacklistKey(array $input_arr, array $blacklist)
    {
        return array_diff_key($input_arr, array_flip($blacklist));
    }
}
