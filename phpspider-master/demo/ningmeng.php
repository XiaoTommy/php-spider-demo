<?php
ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => '柠檬私房歌',
    //'log_show' => true,
    'tasknum' => 1,
    //'save_running_state' => true,
    'domains' => array(
        'ningmeng.name',
        'www.ningmeng.name'
    ),
    'scan_urls' => array(
        'http://www.ningmeng.name/?p=10336'
    ),
    'content_url_regexes' => array(
        "http://www.ningmeng.name/?p=\d+"
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
        'table' => 'ningmeng',
    ),
    'fields' => array(
        array(
            'name' => "song_name",
            'selector_type' => 'regex',
            'selector' => '#i<span style="font-size: 18pt;">([^/]+)</span>#i',
            'required' => true,
        ),
        array(
            'name' => "song_intro",
            'selector_type' => 'regex',
            'selector' => '#i<span style="font-size: 12pt;">([^/]+)</span>#i',
            'required' => true,
        ),
    ),
);

$spider = new phpspider($configs);


$spider->on_extract_field = function($fieldname, $data, $page)
{
    if ($fieldname == 'song')
    {
        if (strlen($data) > 10)
        {
            // 下面方法截取中文会有异常
            //$data = substr($data, 0, 10)."...";
            $data = mb_substr($data, 0, 10, 'UTF-8')."...";
        }
    }
    elseif ($fieldname == 'article_publish_time')
    {
        // 用当前采集时间戳作为发布时间
        $data = time();
    }
    // 把当前内容页URL替换上面的field
    elseif ($fieldname == 'url')
    {
        $data = $page['url'];
    }
    return $data;
};

$spider->start();


