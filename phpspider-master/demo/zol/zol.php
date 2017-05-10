<?php
ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => 'ZOL',
    'log_show' => false,
    'tasknum' => 1,
    //'save_running_state' => true,
    'domains' => array(
        'detail.zol.com.cn'
    ),
    'scan_urls' => array(
        'http://detail.zol.com.cn/cell_phone_index/subcate57_list_1.html'
    ),
    'list_url_regexes' => array(
        "http://detail.zol.com.cn/cell_phone_index/subcate57_0_list_1_0_1_2_0_\d.html"
    ),
    'content_url_regexes' => array(
        "http://detail.zol.com.cn/cell_phone/index\d+.shtml",
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
        'table' => 'zol',
    ),
    'fields' => array(
        array(
            'name' => "mobile_name",
            'selector' => "//div[contains(@class,'wrapper')]//div[contains(@class,'page-title')]/h1",
            'required' => true,
        ),
        array(
            'name' => "mobile_intro",
            'selector' => "//div[contains(@class,'wrapper')]//div[contains(@class,'page-title')]/div[contains(@class,'subtitle')]",
            'required' => true,
        ),
        array(
            'name' => "consult_price",
            'selector' => "//div[contains(@class,'wrapper')]//div[contains(@class,'price price-normal')]//b[contains(@class,'price-type')]/text()",
            'required' => true,
        ),
        array(
            'name' => "showdate",
            'selector' => "//div[contains(@class,'config-section')]//span[contains(@class,'showdate')]",
            'required' => true,
        ),
        array(
            'name' => "score",
            'selector' => "//*[@id=\"totalPoint\"]//div[contains(@class,'score')]/strong",
            'required' => true,
        ),
        array(
            'name' => "screen_size",
            'selector' => "//span[contains(@class,'param-value')]",
            'required' => true,
        ),
        array(
            'name' => "brand",
            'selector' => "//div[contains(@class,'breadcrumb')]/a[3]",
            'required' => true,
        ),
    ),
);

$spider = new phpspider($configs);



$spider->start();
