<?php
namespace app\index\controller;
class Cai
{
    private $_time = 0;
    public function index()
    {
        
    }
    
    public function test() {
        $this->_time = 123;
    }
    
    public function test2() {
        return $this->_time;
    }
}
