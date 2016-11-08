<?php

/**
 * Created by PhpStorm.
 * User: rain
 * Date: 16/11/8
 * Time: 下午2:21
 */

use MegaFilter\MegaFilterClient;

class MegaFilterClientFactory
{
    private static $_instance;

    private static $host = '127.0.0.1:9000';

    private static $token = '1234567';

    /**
     * @param $project
     * @return MegaFilterClient
     */
    public static function getInstance($project)
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new MegaFilterClient(self::$host, $project, self::$token);
        }

        return self::$_instance;
    }
}