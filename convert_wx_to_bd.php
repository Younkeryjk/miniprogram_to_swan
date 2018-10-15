<?php

class Convert_wx_to_bd
{
    private $project_dir;
    private $ext_rules;
    public function __construct($project_dir, $ext_rules = array('wxml' => 'swan', 'wxss' => 'css'))
    {
        $this->project_dir = $project_dir;
        $this->ext_rules = $ext_rules;

        //修改根目录app.wxss后缀及app.js内容
        $this->convert_root();

        //批量修改文件后缀：.wxml转换为.swan，.wxss转换为.css
        $this->convert_ext($this->project_dir.'/'.'pages', $this->ext_rules);

        //批量修改语法部分
        $this->convert_text($this->project_dir.'/'.'pages');
    }

    /**
     *
     */
    private function convert_root() {
        //修改根目录.wxss文件后缀
        rename($this->project_dir.'/'.'app.wxss', $this->project_dir.'/'.'app.css');

        //修改根目录app.js文件语法部分
        $app_js = $this->project_dir.'/'.'app.js';
        $text = file_get_contents($app_js);
        $text = str_replace('wx.', 'swan.', $text);
        file_put_contents($app_js, $text);
    }

    /**
     * 批量修改文件后缀名
     * @param $path 文件夹路径
     * @param $ext_rules 文件后缀替换规则
     * @return void
     */
    private function convert_ext($path, $ext_rules)
    {
        $handle = opendir($path);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != '..') {
                    if (is_dir($path . '/' . $file)) {
                        $this->convert_ext($path . '/' . $file, $ext_rules);
                    } else {
                        $path_info = pathinfo($file);
                        $ext = $path_info['extension'];
                        $all_exts = array_keys($ext_rules);
                        if (in_array($ext, $all_exts)) {
                            $src = $path . '/' . $file;
                            $dext = $ext_rules[$ext];
                            $fileName = $path_info['filename'];
                            $dest = $path . '/' . $fileName . '.' . $dext;
                            rename($src, $dest);
                        }
                    }
                }
            }
        }
    }


    /*
     * js文件：
     * 1、wx.替换为swan.
     * .swan文件：
     * 1、.wxml替换为.swan
     * 2、.wxss替换为.css
     * 循环：
     * 3、wx:for="{{var}}替换为s-for="var"
     * 4、wx:key替换为s-for-index
     * 5、wx:for-item替换为s-for-item
     * 条件：
     * 6、wx:if="{{expression}}替换为s-if="expression"
     * 7、wx:elif="{{expression}}替换为s-elif="expression"
     * 8、wx:else替换为s-else
     * 模板：
     * 9、<template is="var" data="{{{var}}}" />需将data属性两个大括号替换为三个大括号
     */
    private function convert_text($path)
    {
        $handle = opendir($path);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != '..') {
                    if (is_dir($path . '/' . $file)) {
                        $this->convert_text($path . '/' . $file);
                    } else {
                        $path_info = pathinfo($file);
                        $ext = $path_info['extension'];
                        $src = $path . '/' . $file;
                        if ('js' == $ext) {
                            $text = file_get_contents($src);
                            $text = str_replace('wx.', 'swan.', $text);
                            file_put_contents($src, $text);
                        }
                        if ('swan' == $ext) {
                            $text = file_get_contents($src);
                            $text = str_replace('.wxml', '.swan', $text);
                            $text = str_replace('.wxss', '.css', $text);
                            $text = preg_replace("/wx:for=([\"|'])\{\{(.*?)\}\}([\"|'])/", "s-for=$1$2$3", $text);
                            $text = str_replace('wx:key', 's-for-index', $text);
                            $text = str_replace('wx:for-item', 's-for-item', $text);
                            $text = preg_replace("/wx:if=([\"|'])\{\{(.*?)\}\}([\"|'])/", "s-if=$1$2$3", $text);
                            $text = preg_replace("/wx:elif=([\"|'])\{\{(.*?)\}\}([\"|'])/", "s-elif=$1$2$3", $text);
                            $text = str_replace('wx:else', 's-else', $text);
                            $text = preg_replace("/data=([\"|'])\{\{(.*?)\}\}([\"|'])/", "data=$1{{{\$2}}}$3", $text);
                            file_put_contents($src, $text);
                        }
                    }
                }
            }
            return false;
        }
    }
}

$obj = new Convert_wx_to_bd('E:/znxcx/wjsw');
exit('SUCCESS!');