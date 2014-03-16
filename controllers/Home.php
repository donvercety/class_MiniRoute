<?php

class Home {
    public function __construct() {
        echo 'This is the home page. </br />';   
        $this->_other();
    }
    
    protected function _other() {
        echo 'This is the _other function in home, lolz!';  
    }
}