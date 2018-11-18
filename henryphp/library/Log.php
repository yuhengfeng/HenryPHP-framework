<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/5
 * Time: 9:58 AM
 * Email: henry.hyu1175@gmail.com
 */

namespace henryphp;

class Log implements \LoggerInterface
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    const SQL       = 'sql';

    /**
     * 日志信息
     * @var array
     */
    protected $log = [];

    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * 日志写入驱动
     * @var object
     */
    protected $driver;

    /**
     * 日志授权key
     * @var string
     */
    protected $key;

    /**
     * 是否允许日志写入
     * @var bool
     */
    protected $allowWrite = true;

    /**
     * 应用对象
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public static function __make(App $app, Config $config)
    {
        return (new static($app))->init($config->pull('log'));
    }

    /**
     * 日志初始化
     * @access public
     * @param  array $config
     * @return $this
     */
    public function init($config = [])
    {
        $type = isset($config['type']) ? $config['type'] : 'File';

        $this->config = $config;

        unset($config['type']);

        if (!empty($config['close'])) {
            $this->allowWrite = false;
        }

        $this->driver = Loader::factory($type, '\\henryphp\\log\\driver\\', $config);

        return $this;
    }

    /**
     * 获取日志信息
     * @access public
     * @param  string $type 信息类型
     * @return array
     */
    public function getLog($type = '')
    {
        return $type ? $this->log[$type] : $this->log;
    }

    /**
     * 记录日志信息
     * @access public
     * @param  mixed  $msg       日志信息
     * @param  string $type      日志级别
     * @param  array  $context   替换内容
     * @return $this
     */
    public function record($msg, $type = 'info', array $context = [])
    {
        if (!$this->allowWrite) {
            return;
        }

        if (is_string($msg) && !empty($context)) {
            $replace = [];
            foreach ($context as $key => $val) {
                $replace['{' . $key . '}'] = $val;
            }

            $msg = strtr($msg, $replace);
        }

        if (PHP_SAPI == 'cli') {
            // 命令行日志实时写入
            $this->write($msg, $type, true);
        } else {
            $this->log[$type][] = $msg;
        }
        return $this;
    }

    /**
     * 实时写入日志信息 并支持行为
     * @access public
     * @param  mixed  $msg   调试信息
     * @param  string $type  日志级别
     * @param  bool   $force 是否强制写入
     * @return bool
     */
    public function write($msg, $type = 'info', $force = false)
    {
        // 封装日志信息
        if (empty($this->config['level'])) {
            $force = true;
        }

        if (true === $force || in_array($type, $this->config['level'])) {
            $log[$type][] = $msg;
        } else {
            return false;
        }

        // 写入日志
        return $this->driver->save($log, false);
    }

    /**
     * 保存调试信息
     * @access public
     * @return bool
     */
    public function save()
    {
        if (empty($this->log) || !$this->allowWrite) {
            return true;
        }

        if (!$this->check($this->config)) {
            // 检测日志写入权限
            return false;
        }

        if (empty($this->config['level'])) {
            // 获取全部日志
            $log = $this->log;
            if (!$this->app->isDebug() && isset($log['debug'])) {
                unset($log['debug']);
            }
        } else {
            // 记录允许级别
            $log = [];
            foreach ($this->config['level'] as $level) {
                if (isset($this->log[$level])) {
                    $log[$level] = $this->log[$level];
                }
            }
        }

        $result = $this->driver->save($log, true);

        if ($result) {
            $this->log = [];
        }

        return $result;
    }

    /**
     * 检查日志写入权限
     * @access public
     * @param  array  $config  当前日志配置参数
     * @return bool
     */
    public function check($config)
    {
        if ($this->key && !empty($config['allow_key']) && !in_array($this->key, $config['allow_key'])) {
            return false;
        }

        return true;
    }
}