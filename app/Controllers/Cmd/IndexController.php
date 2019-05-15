<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 10/7/18
 * Time: 上午12:13
 */

namespace app\Controllers\Cmd;


use app\Controllers\Controller;

class IndexController extends Controller
{

    public function index()
    {
        $input = $this->request->all();
        dd($input);
    }

}