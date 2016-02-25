<?php

class About {

    public function index(Route $route) {
        echo '<h2>This is the about page. </h2>';
		
		echo "<code>Pretty parameters:</code>";
		var_dump($route->getParams());
		
        $this->_other($route);
    }

    protected function _other($route) {
        echo '<h3>This is the <code>_other()</code> method bout, lolz!</h3>';
		
		echo "<code>Query String parameters:</code>";
        var_dump($route->getData());
    }
}