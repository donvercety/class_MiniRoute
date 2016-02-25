<?php

class About {

    public function index() {
        echo 'This is the about page. </br />';   
        $this->_other();
    }

    protected function _other() {
        echo 'This is the _other functionin about, lolz!'; 
        echo '<pre>', print_r($_GET, TRUE), '</pre>';
    }
}