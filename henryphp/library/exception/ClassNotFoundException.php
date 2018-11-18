<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/1
 * Time: 5:34 PM
 * Email: henry.hyu1175@gmail.com
 */

namespace henryphp\exception;

class ClassNotFoundException extends \RuntimeException
{
    protected $class;

    public function __construct($message, $class = '')
    {
        $this->message = $message;
        $this->class   = $class;
    }

    /**
     * 获取类名
     * @access public
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
