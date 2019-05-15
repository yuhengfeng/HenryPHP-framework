<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/5
 * Time: 3:29 PM
 * Email: henry.hyu1175@gmail.com
 */
namespace henryphp;

class Request
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        // 表单请求类型伪装变量
        'var_method'       => '_method',
        // 表单ajax伪装变量
        'var_ajax'         => '_ajax',
        // 表单pjax伪装变量
        'var_pjax'         => '_pjax',
        // PATHINFO变量名 用于兼容模式
        'var_pathinfo'     => 's',
        // 兼容PATH_INFO获取
        'pathinfo_fetch'   => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
        // 默认全局过滤方法 用逗号分隔多个
        'default_filter'   => '',
        // 域名根，如thinkphp.cn
        'url_domain_root'  => '',
        // HTTPS代理标识
        'https_agent_name' => '',
        // IP代理获取标识
        'http_agent_ip'    => 'HTTP_X_REAL_IP',
        // URL伪静态后缀
        'url_html_suffix'  => 'html',
    ];

    /**
     * 请求类型
     * @var string
     */
    protected $method;

    /**
     * 请求url
     * @var string
     */
    protected $url;

    /**
     * 主机名（含端口）
     * @var string
     */
    protected $host;

    /**
     * 域名（含协议及端口）
     * @var string
     */
    protected $domain;

    /**
     * 子域名
     * @var string
     */
    protected $subDomain;

    /**
     * 当前请求的参数
     * @var array
     */
    protected $param = [];

    /**
     * 是否合并请求参数
     * @var
     */
    protected $mergeParam = false;
    /**
     * 当前GET参数
     * @var array
     */
    protected $get = [];

    /**
     * 当前POST参数
     * @var array
     */
    protected $post = [];
    /**
     * 当前PUT参数
     * @var array
     */
    protected $put = [];
    /**
     * 当前SERVER参数
     * @var array
     */
    protected $server = [];

    /**
     * 资源类型定义
     * @var array
     */
    protected $mimeType = [
        'xml'   => 'application/xml,text/xml,application/x-xml',
        'json'  => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'    => 'text/javascript,application/javascript,application/x-javascript',
        'css'   => 'text/css',
        'rss'   => 'application/rss+xml',
        'yaml'  => 'application/x-yaml,text/yaml',
        'atom'  => 'application/atom+xml',
        'pdf'   => 'application/pdf',
        'text'  => 'text/plain',
        'image' => 'image/png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/webp,image/*',
        'csv'   => 'text/csv',
        'html'  => 'text/html,application/xhtml+xml,*/*',
    ];

    /**
     * 全局过滤规则
     * @var array
     */
    protected $filter;


    public function __construct(array $options = [])
    {
        $this->config = array_merge($this->config, $options);

        if (is_null($this->filter) && !empty($this->config['default_filter'])) {
            $this->filter = $this->config['default_filter'];
        }
    }

    /**
     * @param App $app
     * @param Config $config
     * @return Request
     */
    public static function __make(App $app, Config $config)
    {
        $request = new static($config->pull('app'));

        $request->server = $_SERVER;

        return $request;
    }

    /**
     * 获取server参数
     * @access public
     * @param  string        $name 数据名称
     * @param  string        $default 默认值
     * @return mixed
     */
    public function server($name = '',$default = null)
    {
        if (empty($name)){
            return $this->server;
        }else{
            $name = strtoupper($name);
        }

        return isset($this->server[$name]) ? $this->server[$name] : $default;
    }

    /**
     * 获取当前包含协议、端口的域名
     * @access public
     * @param  bool $port 是否需要去除端口号
     * @return string
     */
    public function domain($port = false)
    {
        return $this->scheme() . '://' . $this->host($port);
    }

    /**
     * 获取当前完整URL 包括QUERY_STRING
     * @access public
     * @param  bool $complete 是否包含域名
     * @return string
     */
    public function url($complete = false)
    {
        if (!$this->url) {
            if ($this->isCli()) {
                $this->url = CMD_URI;
            } elseif ($this->server('HTTP_X_REWRITE_URL')) {
                $this->url = $this->server('HTTP_X_REWRITE_URL');
            } elseif ($this->server('REQUEST_URI')) {
                $this->url = $this->server('REQUEST_URI');
            } elseif ($this->server('ORIG_PATH_INFO')) {
                $this->url = $this->server('ORIG_PATH_INFO') . (!empty($this->server('QUERY_STRING')) ? '?' . $this->server('QUERY_STRING') : '');
            } else {
                $this->url = '';
            }
        }

        return $complete ? $this->domain() . trim($this->url) : trim($this->url);
    }

    /**
     * 获取媒体类型
     * @return bool|int|string
     */
    public function mimeType()
    {
        $accept = $this->server('HTTP_ACCEPT');

        if (empty($accept)) {
            return false;
        }

        foreach ($this->mimeType as $type=>$value)
        {
            $value = explode(',',$value);

            foreach ($value as $key=>$v)
            {
                if (stristr($accept,$v)){
                    return $type;
                }
            }
        }

        return false;
    }

    /**
     * 设置媒体类型
     * @param string|array $type
     * @param string $val
     */
    public function setMimeType($type,$val = '')
    {
        if (is_array($type)){
            $this->mimeType = array_merge($type);
        }else{
            $this->mimeType[$type] = $val;
        }
    }
    /**
     * 当前的请求类型
     * @access public
     * @param  bool $origin  是否获取原始请求类型
     * @return string
     */
    public function method($origin = false)
    {
        if ($origin) {
            // 获取原始请求类型
            return $this->server('REQUEST_METHOD') ?: 'GET';
        } elseif (!$this->method) {
            if (isset($_POST[$this->config['var_method']])) {
                $this->method    = strtoupper($_POST[$this->config['var_method']]);
                $method          = strtolower($this->method);
                $this->{$method} = $_POST;
            } elseif ($this->server('HTTP_X_HTTP_METHOD_OVERRIDE')) {
                $this->method = strtoupper($this->server('HTTP_X_HTTP_METHOD_OVERRIDE'));
            } else {
                $this->method = $this->server('REQUEST_METHOD') ?: 'GET';
            }
        }

        return $this->method;
    }

    /**
     * 获取所有的请求参数
     * @param string $name
     * @param null $default
     * @return array
     */
    public function all()
    {
        if (!$this->mergeParam)
        {
            $method = $this->method(true);

            // 自动获取请求变量
            switch ($method) {
                case 'POST':
                    $vars = $_POST;
                    break;
                case 'PUT':
                case 'DELETE':
                case 'GET':
                    $vars = $_GET;
                    break;
                default:
                    $vars = [];
            }
            $this->param = array_merge($this->param,$vars);

            $this->mergeParam = true;
        }
        //文件上传

        //.........

        return $this->filterParams($this->param);
    }

    /**
     * 过滤 id/1?test=name&kh=9/city/sh/t/1 请求参数
     * @return array
     */
    protected function filterParams($param)
    {
        $uri = str_replace(array("?","=","&"),array("/","/","/"),$this->url());
        $aUri = array_filter(explode("/",$uri));
        $aUri = array_slice($aUri,3);
        $input = array();
        for ($i = 0 ; $i < count($aUri)-1 ; $i ++){
            if ($i % 2 == 0){
                $input[$aUri[$i]] = $aUri[$i+1];
            }
        }
        return array_merge($param,$input);
    }
    /**
     * 获取请求参数
     * 字符串之间可以用'.'符号获取多层参数
     * @param string $name
     * @param null $default
     * @return array|mixed|null
     */
    public function input($name = '',$default = null)
    {
        $data = $this->all();

        $name = (string) $name;

        if ('' != $name) {
            // 解析name
            $data = $this->getData($data, $name);

            if (is_null($data)) {
                return $default;
            }

            if (is_object($data)) {
                return $data;
            }
        }else{
            return $data;
        }

        return $data;
    }

    /**
     *  获取参数的白名单
     */
    public function only(...$arg)
    {
        $data = $this->all();

        if (count($arg) == count($arg,1)){
            $data = $this->unsetVal($data,$arg,false);
        }else{
            if (empty($arg[0])){
                return $data;
            }
            $data = $this->unsetVal($data,$arg[0],false);
        }

        return $data;
    }

    /**
     * 获取参数的黑名单
     */
    public function except(...$arg)
    {
        $data = $this->all();

        if (count($arg) == count($arg,1)){
            $data = $this->unsetVal($data,$arg);
        }else{
            if (empty($arg[0])){
                return $data;
            }
            $data = $this->unsetVal($data,$arg[0]);
        }

        return $data;
    }

    /**
     * @param array $data
     */
    protected function unsetVal(array $data,$params = [],$default = true)
    {
        $arr = [];
        foreach ($data as $key=>$value){
            foreach ($params as $val){
                if ($key == $val){
                    if ($default){
                        unset($data[$key]);
                        $arr = $data;
                    }else{
                        $arr[$val] = $value;
                    }
                }
            }
        }

        return $arr;
    }
    /**
     * 获取数据
     * @access public
     * @param  array         $data 数据源
     * @param  string|false  $name 字段名
     * @return mixed
     */
    protected function getData(array $data, $name)
    {
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return;
            }
        }

        return $data;
    }
    /**
     * 是否为GET请求
     * @access public
     * @return bool
     */
    public function isGet()
    {
        return $this->method() == 'GET';
    }

    /**
     * 是否为POST请求
     * @access public
     * @return bool
     */
    public function isPost()
    {
        return $this->method() == 'POST';
    }
    /**
     * 是否为PUT请求
     * @access public
     * @return bool
     */
    public function isPut()
    {
        return $this->method() == 'PUT';
    }

    /**
     * 是否为DELTE请求
     * @access public
     * @return bool
     */
    public function isDelete()
    {
        return $this->method() == 'DELETE';
    }

    /**
     * 是否为HEAD请求
     * @access public
     * @return bool
     */
    public function isHead()
    {
        return $this->method() == 'HEAD';
    }

    /**
     * 是否为PATCH请求
     * @access public
     * @return bool
     */
    public function isPatch()
    {
        return $this->method() == 'PATCH';
    }

    /**
     * 是否为OPTIONS请求
     * @access public
     * @return bool
     */
    public function isOptions()
    {
        return $this->method() == 'OPTIONS';
    }

    /**
     * 当前URL地址中的scheme参数
     * @access public
     * @return string
     */
    public function scheme()
    {
        return $this->isSsl() ? 'https' : 'http';
    }

    /**
     * 是否为cli
     * @access public
     * @return bool
     */
    public function isCli()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * 当前请求的host
     * @access public
     * @param bool $strict  true 仅仅获取HOST
     * @return string
     */
    public function host($strict = false)
    {
        if (!$this->host) {
            $this->host = $this->server('HTTP_X_REAL_HOST') ?: $this->server('HTTP_HOST');
        }
        if ($this->isCli()){
            return CMD_HOST;
        }else{
            return true === $strict && strpos($this->host, ':') ? strstr($this->host, ':', true) : $this->host;
        }
    }

    /**
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public function isSsl()
    {
        if ($this->server('HTTPS') && ('1' == $this->server('HTTPS') || 'on' == strtolower($this->server('HTTPS')))) {
            return true;
        } elseif ('https' == $this->server('REQUEST_SCHEME')) {
            return true;
        } elseif ('443' == $this->server('SERVER_PORT')) {
            return true;
        } elseif ('https' == $this->server('HTTP_X_FORWARDED_PROTO')) {
            return true;
        } elseif ($this->config['https_agent_name'] && $this->server($this->config['https_agent_name'])) {
            return true;
        }

        return false;
    }
    /**
     * 获取配置中的某个值
     * @param null $name
     * @return array|mixed|null
     */
    public function config($name = null)
    {
        if (is_null($name)) {
            return $this->config;
        }
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * 获取当前的参数
     * @return array|bool|string
     */
    public function getCurUriParam()
    {
        if ($name = $this->filterCurUrl()){
            array_shift($name);
            array_shift($name);
            array_shift($name);
            return $name;
        }

        return array();
    }
    /**
     * 获取当前模块
     * @return mixed|string
     */
    public function getModuleName()
    {
        if ($name = $this->filterCurUrl()){
            return ucfirst(current($name));
        }
        return config('app.modules.default');
    }

    /**
     * 获取当前控制器名称
     * @return array|mixed|null|string
     */
    public function getControllerName()
    {
        if ($name = $this->filterCurUrl()){
            return  ucfirst(next($name));
        }
        return config('app.modules.default_controller');
    }

    /**
     * 获取当前方法名称
     * @return array|mixed|null|string
     */
    public function getActionName()
    {
        if ($name = $this->filterCurUrl()){
            next($name);
            return  ucfirst(next($name));
        }
        return config('app.modules.default_action');
    }
    /**
     * 过滤当前的url
     * @return bool|string
     */
    protected function filterCurUrl()
    {
        $url = $this->url();
        // 清除?之后的内容
        $position = strpos($url, '?');
        $url = $position === false ? $url : substr($url, 0, $position);

        // 删除前后的“/”
        $url = trim($url, '/');

        return $url ? array_filter(explode('/', $url)) : $url;
    }

}