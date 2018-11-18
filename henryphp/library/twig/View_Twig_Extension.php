<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/7
 * Time: 10:32 AM
 * Email: henry.hyu1175@gmail.com
 */
namespace henryphp\twig;


class View_Twig_Extension
{
    /**
     * 添加php函数
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return array(
            new \Twig_Function('config', function($name = null, $default = null){
                return config($name, $default);
            }),
            new \Twig_Function('asset', function($name = null){
                return asset($name);
            }),
            new \Twig_Function('app', function($name = 'henryphp\App', $args = [], $newInstance = false){
                return app($name, $args, $newInstance);
            }),
        );
    }

    /**
     *  注册php函数
     * @param \Twig_Environment $twig_Environment
     */
    public function registerFunction(\Twig_Environment $twig_Environment)
    {
        $function = $this->getFunctions();

        foreach ($function as $k=>$val)
        {
            $twig_Environment->addFunction($val);
        }
    }
}
