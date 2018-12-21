<?php

namespace apps\httpd\controllers;

use Mix\Http\Controller;

/**
 * 默认控制器
 * @author 刘健 <coder.liu@qq.com>
 */
class IndexController extends Controller
{

    // 默认动作
    public function actionIndex()
    {
        return $this->render("index.html.twig");
    }
}
