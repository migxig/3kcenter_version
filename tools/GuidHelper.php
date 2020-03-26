<?php

/**
 * 获取客户端guid或者获取服务端guid
 */
class GuidHelper
{
    private static $guidNumber = [
        0   => 0,
        1   => 'k',
        2   => 4,
        3   => 'y',
        4   => 'j',
        5   => 6,
        6   => 9,
        7   => 'o',
        8   => 'v',
        9   => 'a',
        'a' => 'm',
        'b' => 'i',
        'c' => 'b',
        'd' => 'd',
        'e' => 's',
        'f' => 'l',
        'g' => 'u',
        'h' => 1,
        'i' => 'x',
        'j' => 'g',
        'k' => 't',
        'l' => 'q',
        'm' => 'n',
        'n' => 2,
        'o' => 3,
        'p' => 'p',
        'q' => 'h',
        'r' => 'r',
        's' => 'w',
        't' => 'f',
        'u' => 'c',
        'v' => 'e',
        'w' => 'z',
        'x' => 8,
        'y' => 5,
        'z' => 7,
    ];

    /**
     * 通过guid明文获取密文
     * @param $server_guid
     * @return string
     */
    public static function encode($server_guid)
    {
        if ($server_guid <= 0 || !is_numeric($server_guid)) {
            return "";
        }
        
        $server_guid     = intval($server_guid);
        $server_guid_str = base_convert($server_guid, 10, 36);
        $str             = "";
        for ($i = 0, $len = strlen($server_guid_str); $i < $len; $i++) {
            $k = $server_guid_str[$i];
            $str .= self::$guidNumber[$k];
        }

        return (string) $str;
    }

    /**
     * 通过guid密文转明文
     * @param $client_guid
     * @return int|string
     */
    public static function decode($client_guid)
    {
        if (trim($client_guid) === "") {
            return "";
        }
        $client_guid         = \strtolower($client_guid);
        $client_guid         = trim($client_guid);
        $guid_number_reverse = array_flip(self::$guidNumber);
        $str                 = "";
        for ($i = 0, $len = strlen($client_guid); $i < $len; $i++) {
            $k = $client_guid[$i];
            $str .= $guid_number_reverse[$k];
        }

        return intval(base_convert($str, 36, 10));
    }

}
