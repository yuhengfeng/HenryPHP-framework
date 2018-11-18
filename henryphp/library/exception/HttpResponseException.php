<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 25/6/18
 * Time: 下午2:22
 */

namespace henryphp\exception;

use Exception;

class HttpResponseException extends Exception
{
    protected $msgForUser;

    public function __construct($message, $msgForUser = '系统内部错误',  $code = 500)
    {
        parent::__construct($message, $code);

        $this->msgForUser = $msgForUser;
    }

    public function render()
    {
        return $this->msgForUser;
//        if ($request->expectsJson()) {
//            return response()->json(['msg' => $this->msgForUser], $this->code);
//        }

//        return view('pages.error', ['msg' => $this->msgForUser]);
    }
}
