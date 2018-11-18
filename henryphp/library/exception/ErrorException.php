<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/1
 * Time: 5:34 PM
 * Email: henry.hyu1175@gmail.com
 */

namespace henryphp\exception;

class ErrorException extends \Exception
{
    /**
     * 用于保存错误级别
     * @var integer
     */
    protected $severity;

    /**
     * 错误异常构造函数
     * @access public
     * @param  integer $severity 错误级别
     * @param  string  $message  错误详细信息
     * @param  string  $file     出错文件路径
     * @param  integer $line     出错行号
     */
    public function __construct($severity, $message, $file, $line)
    {
        $this->severity = $severity;
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
        $this->code     = 0;
    }

    /**
     * 获取错误级别
     * @access public
     * @return integer 错误级别
     */
    final public function getSeverity()
    {
        return $this->severity;
    }
}