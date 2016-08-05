<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\index\api;


class Mycrawler extends \phpfetcher\Crawler\Main{
    public function handlePage($page) {
        //打印处当前页面的title
        $res = $page->sel('//a');
        echo '<pre>';
        for ($i = 0; $i < count($res); ++$i) {
            echo $res[$i]->plaintext;
            echo "\n";
            echo $res[$i]->getAttribute('href');
            echo "\n";
            echo "\n";
        }
    }
    
}
