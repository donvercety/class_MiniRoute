<?php

class Contact {
    public function __construct() {
        echo 'This is the cintact page. </br />';   
        $this->_other();
    }
    
    protected function _other() {
        echo 'This is the _other function in contact, lolz!';  
    }
}