<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/7
 * Time: 10:36 AM
 * Email: henry.hyu1175@gmail.com
 */
namespace henryphp\twig;

class View_Twig
{
    protected $config = [
        //模版后缀
        'template_prefix' => 'twig',
        'view_path' => 'resources/views/',
        //视图缓存
        'cache' => true,
        'cache_path' => 'storage/view/cache'
    ];

    protected $extension;

    public function __construct()
    {
        $this->config = array_merge(config('view'),$this->config);
        $this->extension = new View_Twig_Extension();
    }

    /**
     * @param array $data
     * @param $path
     * @param $filename
     * @param $namespace
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function view(array $data = [],$path,$filename,$namespace = 'http')
    {
        $loader = new \Twig_Loader_Filesystem($path);

        $loader->addPath($path, $namespace);

        if ($this->config['cache']){
            $params = [
                'cache' => ROOT_PATH.$this->config['cache_path'],
                'auto_reload' => $this->config['cache']
            ];
        }else{
            $params = [];
        }

        $twig = new \Twig_Environment($loader, $params);

        //函数扩展
        $this->extension->registerFunction($twig);

        echo $twig->render($filename, $data);
    }
}