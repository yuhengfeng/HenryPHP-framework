<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 10/7/18
 * Time: 上午12:13
 */

namespace app\Controllers\Cmd;


use app\Controllers\Controller;
use app\Models\Articles;

class IndexController extends Controller
{

    public function index()
    {
        $test = config();
        $users = Articles::all();
        dd($test);
    }

}