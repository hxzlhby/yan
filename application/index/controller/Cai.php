<?php
namespace app\index\controller;
use Ares333\CurlMulti\Core;
use QL\QueryList;
class Cai
{
    private $_time = 0;
    private $_url = 'http://www.cnblogs.com/nixi8';
    public function index()
    {
        $curl = new Core ();
        
        $url = "http://so.gushiwen.org/type.aspx?p=1&c=%E5%94%90%E4%BB%A3";
        $cookie = '__utma=51854390.342890529.1460530311.1460530311.1460530311.1;__utmb=51854390.2.10.1460530311;__utmt=1;__utmv=51854390.000--|3=entry_date=20160413=1;__utmz=51854390.1460530311.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none);_xsrf=60b34b9bf403167f3c565ab313bdf24b;_za=59f006ac-4dca-491b-ac81-bcac14c56bcb;_zap=56da1fcc-abc2-4659-ad3f-0b0090fa46be;a_t="2.0AACDUBtknAcXAAAAaZHKVwAAg1AbZJwHAJAAEdCswwkXAAAAYQJVTaaOylcAh0gniYdDnDCDc9lTIaPQpFC5NGLUWdLfsRpGvet7XOpQUoIwzoR_8Q==";cap_id="MjQwYmE0ZTg2MDhlNDgxYTg2NDkyZjRjOGRlMGU0NTM=|1470300337|ef74bf0ed3453ee349d20b4cf05f804dcac3f4fb"; expires=Sat, 03 Sep 2016 08:44:22 GMT;d_c0="AJAAEdCswwmPTtULEc5gJc4OlRqJP5ZFeLQ=|1460530352";l_cap_id="YmQxYmE4NzEzZGUyNGU1Mzk1MDNkM2E0OTZlNmNhZTk=|1470300337|baeb768444c5f67a09d1432f56a1b9e244a8437b";l_n_c=1;n_c=1;q_c1=06f5e9e6c59e49f48217766489ca6e24|1470300337000|1460530354000;s-i=15;s-q=php;sid=cd0tq04o;z_c0=Mi4wQUFDRFVCdGtuQWNBa0FBUjBLekRDUmNBQUFCaEFsVk5wbzdLVndDSFNDZUpoME9jTUlOejJWTWhvOUNrVUxrMFln|1470300582|67d8bae143b15495060a9aea1b987f62bc5d7a8a;';
        $header = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36 LBBROWSER',
            'Accept-Language: zh-CN,zh;q=0.8'
        );
        
//         $curl->options = [CURLOPT_COOKIE=> $cookie,CURLOPT_HTTPHEADER=>$header];
        
        $curl->add ( array (
            'url' => $this->_url
        ), function ($r) {
            $html = new \HtmlParser\ParserDom($r['content']);
            $ret = $html->find('.day');
            foreach ($ret as $p){
                dump($p->getPlainText());
            }
            dump($ret);
//             preg_match_all("/<a.*?href=\"(.+?)\"/", $r['content'], $matcharr);
//             preg_match_all("/=\"typeleft\">\s*(.*?)=\"right\"/s", $r['content'], $mainPage);
//             dump($matcharr);
//             dump($r['content']);
        } );
        $curl->start ();
        
        
    }
    
    public function test() {
        $crawler = new \app\index\api\Mycrawler();
        $arrJobs = array(
            //任务的名字随便起，这里把名字叫qqnews
            //the key is the name of a job, here names it qqnews
            'qqnews' => array(
                'start_page' => 'http://www.cnblogs.com/nixi8', //起始网页
                'link_rules' => array(
                    /*
                     * 所有在这里列出的正则规则，只要能匹配到超链接，那么那条爬虫就会爬到那条超链接
        * Regex rules are listed here, the crawler will follow any hyperlinks once the regex matches
        */
                ),
                //爬虫从开始页面算起，最多爬取的深度，设置为1表示只爬取起始页面
                //Crawler's max following depth, 1 stands for only crawl the start page
                'max_depth' => 2,
            ) ,
        );
        //$crawler->setFetchJobs($arrJobs)->run(); //这一行的效果和下面两行的效果一样
        $crawler->setFetchJobs($arrJobs);
        $crawler->run();
        
        
        
    }
    
    public function test2() {
        //要采集的目标网址
        $url = "http://www.leiphone.com/";
        //元素选择器
        $reg = array(
            "title" => array(".tit","text"),
        );
        //块选择器
        //采集
        $hj = QueryList::Query($url,$reg);
        //输入采集结果
        dump($hj->data);        
    }
}
