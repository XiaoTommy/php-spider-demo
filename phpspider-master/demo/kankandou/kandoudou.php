<?php
ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => '看豆豆',
//    'log_show' => false,
    'tasknum' => 1,
    //'save_running_state' => true,
    'domains' => array(
        'kankandou.com',
        'www.kankandou.com'
    ),
    'scan_urls' => array(
        'https://kankandou.com/'
    ),
    'list_url_regexes' => array(
        "https://kankandou.com/book/page/\d+"
    ),
    'content_url_regexes' => array(
        "https://kankandou.com/book/view/\d+.html",
    ),
    'max_try' => 5,
    //'export' => array(
    //'type' => 'csv',
    //'file' => PATH_DATA.'/qiushibaike.csv',
    //),
    //'export' => array(
    //'type'  => 'sql',
    //'file'  => PATH_DATA.'/qiushibaike.sql',
    //'table' => 'content',
    //),
    'export' => array(
        'type' => 'db',
        'table' => 'kankandou',
    ),
    'fields' => array(
        array(
            'name' => "book_name",
            'selector' => "//h1[contains(@class,'title')]/text()",
            'required' => true,
        ),
        array(
            'name' => "book_content",
            'selector' => "//div[contains(@class,'content')]/text()",
            'required' => true,
        ),
        array(
            'name' => "book_author",
            'selector' => "//p[contains(@class,'author')]/a",
            'required' => true,
        ),
        array(
            'name' => "book_img",
            'selector' => "//div[contains(@class,'img')]/a/img",
            'required' => true,
        ),
        array(
            'name' => "book_format",
            'selector' => "//p[contains(@class,'ext')]",
            'required' => true,
        ),
        array(
            'name' => "book_class",
            'selector' => "//p[contains(@class,'cate')]/a",
            'required' => true,
        ),
        array(
            'name' => "click_num",
            'selector' => "//i[contains(@class,'dc')]",
            'required' => true,
        ),
        array(
            'name' => "download_num",
            'selector' => "//i[contains(@class,'vc')]",
            'required' => true,
        ),
    ),
);

$spider = new phpspider($configs);

$spider->on_handle_img = function($fieldname, $img)
{
    $regex = '/src="(https?:\/\/.*?)"/i';
    preg_match($regex, $img, $rs);
    if (!$rs)
    {
        return $img;
    }

    $url = $rs[1];
    $img = $url;

    //$pathinfo = pathinfo($url);
    //$fileext = $pathinfo['extension'];
    //if (strtolower($fileext) == 'jpeg')
    //{
    //$fileext = 'jpg';
    //}
    //// 以纳秒为单位生成随机数
    //$filename = uniqid().".".$fileext;
    //// 在data目录下生成图片
    //$filepath = PATH_ROOT."/images/{$filename}";
    //// 用系统自带的下载器wget下载
    //exec("wget -q {$url} -O {$filepath}");

    //// 替换成真是图片url
    //$img = str_replace($url, $filename, $img);
    return $img;
};


$spider->start();
