<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/1
 * Time: 5:39 PM
 * Email: henry.hyu1175@gmail.com
 */

namespace henryphp\exception;

use Exception;
use henryphp\Container;

class Handle
{
    public function convertExceptionToResponse(Exception $exception)
    {
        if (config('app.app_debug')){
            // 调试模式，获取详细的错误信息
            $data = [
                'name'    => get_class($exception),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace'   => $exception->getTraceAsString(),
                'code'    => $exception->getCode(),
                'source'  => $this->getSourceCode($exception),
                'tables'  => [
                    'GET Data'              => $_GET,
                    'POST Data'             => $_POST,
                    'Files'                 => $_FILES,
                    'Cookies'               => $_COOKIE,
                    'Session'               => isset($_SESSION) ? $_SESSION : [],
                    'Server/Request Data'   => $_SERVER,
                    'Environment Variables' => $_ENV,
                    'HenryPHP Constants'    => $this->getConst(),
                ],
            ];
        }else{
            // 部署模式仅显示 name 和 Message 不显示详细错误信息
            $data = [
                'name'    => get_class($exception),
                'message' => config('app.error_message'),
                'trace'   => $exception->getTraceAsString(),
            ];
        }
        //保留一层
        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        $data['echo'] = ob_get_clean();

        ob_start();
        extract($data);

        $logs = $exception->getMessage()."\r\n".$exception->getTraceAsString();
        //写入日志
        Container::get('log')->write($logs, 'error');

        include config('app.exception_tmpl');
    }

    /**
     * 获取出错文件内容
     * 获取错误的前9行和后9行
     * @access protected
     * @param  \Exception $exception
     * @return array 错误文件内容
     */
    protected function getSourceCode(Exception $exception)
    {
        // 读取前9行和后9行
        $line  = $exception->getLine();
        $first = ($line - 9 > 0) ? $line - 9 : 1;

        try {
            $contents = file($exception->getFile());
            $source   = [
                'first'  => $first,
                'source' => array_slice($contents, $first - 1, 19),
            ];
            $str = '';
            $sources = array_combine(range($first,$first+(count($source['source'])-1)),$source['source']);
            foreach ($sources as $key=>$value)
            {
                $font = in_array($key,range($exception->getLine()-2,$exception->getLine())) ? 'show_weight' : '';
                $str .= "<li><span class='$font'>$key.&nbsp;&nbsp;&nbsp;$value</span></li>";
            }
            $source['source'] = $str;
        } catch (Exception $e) {
            $source = [];
        }

        return $source;
    }

    /**
     * 获取常量列表
     * @access private
     * @return array 常量列表
     */
    private static function getConst()
    {
        $const = get_defined_constants(true);

        return isset($const['user']) ? $const['user'] : [];
    }
}