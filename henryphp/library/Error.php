<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/1
 * Time: 5:03 PM
 * Email: henry.hyu1175@gmail.com
 */

namespace henryphp;

use henryphp\Exception\ErrorException;
use henryphp\Exception\Handle;
use henryphp\exception\ThrowableError;

class Error
{
    protected static $exceptionHandler;

    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);//错误
        set_exception_handler([__CLASS__, 'appException']);//异常
        register_shutdown_function([__CLASS__, 'appShutdown']);//中止回调
    }

    /**
     * Error Handler
     * @access public
     * @param  integer $errno   错误编号
     * @param  integer $errstr  详细错误信息
     * @param  string  $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @throws ErrorException
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline);

        if (error_reporting() & $errno) {
            // 将错误信息托管至
            throw $exception;
        }

        self::getExceptionHandler()->convertExceptionToResponse($exception);
    }

    /**
     * Exception Handler
     * @access public
     * @param  \Exception|\Throwable $e
     */
    public static function appException($e)
    {
        if (!$e instanceof \Exception) {
            $e = new ThrowableError($e);
        }

        self::getExceptionHandler()->convertExceptionToResponse($e);
    }

    /**
     * Shutdown Handler
     * @access public
     */
    public static function appShutdown()
    {
        if (!is_null($error = error_get_last()) && self::isFatal($error['type']))
        {

            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);

            self::appException($exception);
        }
        // 写入日志
        Container::get('log')->save();
    }

    /**
     * Get an instance of the exception handler.
     *
     * @access public
     * @return Handle
     */
    public static function getExceptionHandler()
    {
        static $handle;

        if (!$handle) {
            // 异常处理handle
            $class = self::$exceptionHandler;

            if ($class && is_string($class) && class_exists($class) && is_subclass_of($class, "\\henryphp\\Exceptions\\Handle")) {
                $handle = new $class;
            } else {
                $handle = new Handle();
            }
        }

        return $handle;
    }

    /**
     * 确定错误类型是否致命
     *
     * @access protected
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }
}