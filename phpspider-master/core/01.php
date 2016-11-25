<?php 
/****
布尔教育 高端PHP培训
培  训: http://www.itbool.com
论  坛: http://www.zixue.it
****/

---------start() 入口文件
------------->$this->parse_command() //检查运行命令的参数
------------->version_compare()//检查php是否5.3以上
------------->function_exists(curl_init)//检查是否开启curl
------------->function_exists(pcntl_fork)//检查是否是多进程,如果是判断是否开启pcntl
------------->extension_loaded(redis) //如果是多进程，则判断是否开启redis
------------->export_auth() //验证导出项
------------->is_scan_page() //判断是否是入口页
------------->del_task_status // 多任务和分布式都要清掉，当然分布式只清自己的
------------->cache_clear //清空Redis里面上次爬取的采集数据
			  -------->  cls_redis::del("collect_queue");// 删除队列
			  -------->  cls_redis::del("fields_num"); // 删除采集到的field数量
			  -------->  cls_redis::del("collect_urls_num");  // 抓取和抓取到数量
			  -------->  cls_redis::del("collected_urls_num");
			  -------->  cls_redis::keys("collect_urls-*"); // 删除等待采集网页缓存
------------->add_scan_url($url, null, false); //添加入口URL到队列
			  --------> $this->queue_lpush($link, $allowed_repeat); //从队列左边插入
					    -------->cls_redis::lock($lock)  // 加锁：一个进程一个进程轮流处理
						-------->cls_redis::exists($key) // exists key值是否存在
						-------->cls_redis::incr("collect_urls_num"); //待爬取网页记录数加一
						-------->cls_redis::lpush("collect_queue", $link);  // 入队列
						-------->cls_redis::unlock($lock); 	// 解锁
------------->$this->on_start  // 可以添加入口页面 回调函数
------------->queue_lsize()  //队列长度
			 -------->cls_redis::lsize("collect_queue")//获取队列长度
			 如没开启redis
			 -------->count(self::$collect_queue)//获取队列长度
------------->while( $this->queue_lsize(队列长度) ) //循环开始
			    -------->collect_page()  //爬取页面
						 -------->get_collect_url_num() //发现爬取网页数量
						 -------->queue_lsize  //队列长度
						 -------->get_collected_url_num() // 等待爬取网页数量
						 -------->queue_rpop() // 先进先出
						 -------->request_url() // 下载网页，得到网页内容
									// 得到的编码如果不是utf-8的要转成utf-8，因为xpath只支持utf-8
									  -------->requests::$output_encoding = 'utf-8';
									  -------->requests::set_timeout(self::$configs['timeout']);
									  -------->requests::set_timeout(self::$configs['timeout']);
									  -------->requests::set_useragent(self::$configs['user_agent']);
									  // 是否设置了代理
									  --------> requests::set_proxies(array('http'=>$link['proxy'], 'https'=>$link['proxy']));
									  --------> requests::add_header('Proxy-Switch-Ip', 'yes'); // 自动切换IP
										foreach ($link['headers'] as $k=>$v) 	   // 如果设置了 HTTP Headers
										{
											requests::add_header($k, $v);
										}
									  --------> requests::$method($url, $link['params']); //获取页面内容 
									  --------> requests::$status_code; //获取http状态200 500 304 403 501等 
									  --------> $this->on_status_code; //回调函数，根据状态可以做一些处理
									  --------> requests::$status_code; //获取http状态200 500 304 403 501等 
									  --------> $http_code != 200 
											   301,301 // 如果是301、302跳转，抓取跳转后的网页内容
											   --------> $this->request_url($url, $options);
											   如果是 407
											   --------> $this->request_url($url, $options);
												// 扔到队列头部去，继续采集
												$this->queue_rpush($link);
											   --------> in_array($http_code, array('0','502','503','429'))
											   // 扔到队列头部去，继续采集
											   $this->queue_rpush($link);   
						 -------->$this->is_anti_spider // 判断当前网页是否被反爬虫了, 需要开发者实现 回调函数
						 -------->$this->on_download_page() // 在一个网页下载完成之后调用. 主要用来对下载的网页进行处理.
						 -------->$this->on_scan_page() //  scan_page //回调函数
						 -------->$this->on_list_page() //  list_page //回调函数
						 -------->$this->content_page() //  content_page //回调函数
						 // 如果深度没有超过最大深度，获取下一级URL
						 --------> $this->get_html_urls($page['raw'], $url, $link['depth'] + 1);// 分析提取HTML页面中的URL
									 -------->preg_match_all("/<a.*href=[\"']{0,1}(.*)[\"']{0,1}[> \r\n\t]{1,}/isU", $html, $matchs); //正则匹配URL
									 -------->array_unique($urls);去除重复的RUL
									 -------->foreach (){$this->fill_url()} //获得完整的连接地址
									 -------->$this->add_url($url, $options, $depth); //把抓取到的URL放入队列
											  --------> $this->is_list_page($url) //判断是否列表页面
											  --------> $this->queue_lpush($url) //从队列左边插入
											  或
											  --------> $this->is_content_page($url) //判断是否内容页面
											  --------> $this->queue_lpush($url) //从队列左边插入
						 // 如果是内容页，分析提取HTML页面中的字段
						 -------->$this->get_html_fields($page['raw'], $url, $page);
								    --------> $this->get_fields($self::$configs['fields'], $html, $url, $page) 返回数组，key->value数组
											//  如果这个field是上一个field的附带连接
											-------->  $this->fill_url($url, $fields[$conf['attached_url']]); //获取完整连接
											// 取出上个field的内容作为连接，内容分页是不进队列直接下载网页的
											--------> $this->request_url($collect_url); 走下载网页流程
											--------> $this->on_attached_download_page //在一个attached_url对应的网页下载完成之后调用. 主要用来对下载的网页进行处理.
											--------> $this->get_fields // 递归调用本方法，所以多少子项目都支持
											$fields 不等于空
													-------->foreach
															回调函数--------> $this->on_handle_img && preg_match($pattern, $data) //在抽取到field内容之后调用, 对其中包含的img标签进行回调处理
															回调函数--------> $this->on_extract_field //当一个field的内容被抽取到后进行的回调, 在此回调中可以对网页中抽取的内容作进一步处理
									-------->$this->on_extract_page //回调函数 作用于对多表操作						 
									-------->$this->on_extract_page	提取到的field数目加一	
									--------> 最后导出数据
						 -------->$this->incr_depth_num($link['depth']); // 如果当前深度大于缓存的，更新缓存
				// fork 子进程前一定要先干掉redis连接fd，不然会存在进程互抢redis fd 问题		 
				-------->$this->fork_one_task($i); 如果是多任务，则循环进程数，创建子进程
						-------->pcntl_fork();创建一个子进程
						-------->$this->queue_lsize();队列长度
						-------->$this->collect_page();走抓取页面流程
						-------->set_task_status();设置任务状态，主进程和子进程共用
				-------->set_task_status();设置任务状态，主进程和子进程共用		
				unix/linux
				-------->$this->display_ui();	 // 每采集成功一次页面，就刷新一次面板	
				-------->$this->cache_clear();	 //清空Redis里面上次爬取的采集数据
						 



						
						
						
						

?>