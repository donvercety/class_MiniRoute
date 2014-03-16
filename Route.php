<?php
/**
 *  Routing Class
 */
class Route {
	
	/* Private Declaration */
	private $_uri = array(),
            $_method = array();
	
	/**
	 * Building a collection of internal URL's to look for
	 * @param type $uri
	 */
	public function add($uri, $method = NULL) {
        $this->_uri[] = '/' . trim($uri, '/');
        
        if($method != NULL) {
            $this->_method[] = $method;
        }
	}
	
	/**
	 * Makes the thing run
	 */
	public function submit() {
		// $_GET['uri'] comes form the .htaccess file
        $uriGetParam = isset($_GET['uri']) ? '/' .$_GET['uri'] : '/';
 
		foreach($this->_uri as $key => $value) {
            if(preg_match("#^$value$#", $uriGetParam)) {
                
                if(is_string($this->_method[$key])) {
                    // if there is a match we load the chosen CLASS
                    $useClass = $this->_method[$key];
                    new $useClass(); 
                } else {
                    // if there is a match we load the chosen FUNCTION
                    call_user_func($this->_method[$key]);
                }

            }
		}
	}
  
}