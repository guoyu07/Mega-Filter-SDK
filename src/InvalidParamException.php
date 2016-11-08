<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 16/10/17
 * Time: 下午6:21
 */

namespace MegaFilter;

use Exception;

class InvalidParamException extends Exception
{
    public function getName()
    {
        return 'Invalid Parameter';
    }
}