<?php
namespace app\index\controller;
use QL\QueryList;

class QueryList
{
    public function index()
    {
        $url = "http://tech.163.com/";
        //元素选择器
        $rules = array(
            //采集id为one这个元素里面的纯文本内容
            'text' => array('h3','text'),
            //采集class为two下面的超链接的链接
            'link' => array('.hb_detail>a','href'),
            //采集class为two下面的第二张图片的链接
            'img' => array('.img_box>a>img','src'),
            //采集span标签中的HTML内容
        );
        //块选择器
        $rang = ".hot-news>.hot_board";
        //采集
        $data = QueryList::Query($url,$rules,$rang,'UTF-8')->data;
        //输入采集结果
        dump($data);
        
    }
    
    public function test() {
        
    }
    
    public function test2() {
        return $this->_time;
    }
}
