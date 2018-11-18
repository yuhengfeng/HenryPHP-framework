<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 10/7/18
 * Time: 上午12:13
 */

namespace app\Controllers\Admin;

use app\Controllers\Controller;
use app\Models\Articles;

class ArticleController extends Controller
{
    protected $articles;

    public function __construct(Articles $articles)
    {
        parent::__construct();

        $this->articles = $articles;
    }

    public function index()
    {
//        $data = $this->articles->getAll();
//
//        dd($data);

        $this->view('list');
    }
}