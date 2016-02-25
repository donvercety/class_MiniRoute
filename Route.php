<?php
/**
 * Mini Route v2.0
 *
 * Implements function or class callbacks for a specific url.
 * It implements beautiful urls with '/' separation for parameters.
 * It can detect which parameter is a class, method or a separate function,
 * all the parameters are accessible as a method of the Route class
 * use dependency injection, to get hold of the Route instance
 *
 * @author Tommy Vercety
 */
class Route {
    
    private $_uri = array(),
            $_controller = array(),
            $_method,
			$_params,
            $_match = 0;

	/**
     * Building a collection of internal URL's to look for.
     * 
     * @param string $uri
     * @param string $controller
     */
    public function add($uri, $controller = NULL) {
        $this->_uri[] = '/' . trim($uri, '/');
        
        if($controller != NULL) {
            $this->_controller[] = $controller;
        }
    }
    
    /**
     * Makes the thing run
     */
    public function submit() {

        // $_GET['uri'] comes form the .htaccess file
        $uriGetParam = isset($_GET['uri']) ? '/' . $_GET['uri'] : '/';
		
		// unset 'uri' parameter to be able to work with the raw query string
		unset($_GET['uri']);

        // We trim the last '/' if there is any and we check that we do not trim the root '/'
        $uriGetParam = (strlen($uriGetParam) >= 2) ? rtrim($uriGetParam, "/") : $uriGetParam;

        // Making array() from $_GET['uri'].
        $uriGetParam = explode('/', $uriGetParam);

        // adding '/' to each array entry
        foreach($uriGetParam as $param) {
			$params[] = '/' . $param;
		}

        // Setting the class and method variables
        $this->_method = isset($params[2]) ? ltrim($params[2], "/") : NULL;

        foreach($this->_uri as $key => $value) {
            if(preg_match("#^$value$#", $params[1])) {
                $this->_match = 1;

				// if string then we call a php class
                if(is_string($this->_controller[$key])) {
                    $this->_callClassCallback($key, $params);

				// else we assume it's a function callback
				} else {                   
                    $this->_callFunctionCallback($key, $params);
                } 
            }
        }
		
		// if there is no match, load the 404 page
        if (!$this->_match) {
			$this->_notFound();
		}
    }

    /**
     * Method to be executed on a non-existing url.
     * @return void
     */
    private function _notFound() {
        echo '404: Page not found!';
    }
	
	/**
	 * Get URI Parameters
	 * @return array
	 */
	public function getParams() {
		return $this->_params;
	}

	/**
	 * Set URI Parameters
	 * @param array $_params
	 */
	private function _setParams($_params) {
		$this->_params = $_params;
	}
	
	// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	// :: Callbacks
	// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

    /**
     * This method is called when we pass a string
     * as a second parameter in the add() function
     * this means that we are trying to call a php class.
     * 
     * @param  string $key      current iteration key
     * @param  array  $params   optional
     * @return void
     */
    private function _callClassCallback( $key, $params ) {   
        $count    = 1;
        $values   = array();
        $useClass = $this->_controller[$key]; // class name to be loaded
        
		// if we have 2nd parametur we check if it is a method or a value
        if (isset($params[2])) {
            $class = new $useClass();

			// if method we get the values form the 3rd URI param
            if(method_exists($class, $this->_method))  {
                for ($i = 3; $i < count($params); $i++) {
                    $values[$count] = trim($params[$i], '/');
                    $count++;
                }
				
				$this->_setParams($values);
                call_user_func_array(array($class, $this->_method), array($this));
            
			// if not we get the values form the 2nd URI param
			} else {
                for ($i = 2; $i < count($params); $i++) {
                    $values[$count] = trim($params[$i], '/');
                    $count++;
                }
				
				$this->_setParams($values);
                call_user_func_array(array($class, 'index'), array($this));
            }
			
		// if we dont have 2nd parametur we just load the class
        } else { 
            for ($i = 2; $i < count($params); $i++) {
                $values[$count] = trim( $params[$i], '/' );
                $count++;
            }

            $class = new $useClass(); 
			$this->_setParams($values);
            call_user_func_array(array($class, 'index'), array($this));
        }
    }

    /**
     * This method is called when we pass a
     * function callback in the add() method
     *
     * @param  string $key      current iteration key
     * @param  array  $params   optional
     * @return void
     */
    private function _callFunctionCallback($key, $params) {   
        $count  = 1;
        $values = array();

        for ($i = 2; $i < count($params); $i++) {
            $values[$count] = trim($params[$i], '/');
            $count++;
        }

		$this->_setParams($values);
        call_user_func($this->_controller[$key]);
    }

}
