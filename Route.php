<?php

/**
 * Mini Route v2.2
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

    private $_uri        = array(),
            $_controller = array(),
            $_params     = NULL,
            $_getData    = NULL,
            $_indexFile  = TRUE,
            $_match      = 0,
            $_method;

	/**
	 * Constructor, settings can be 
	 * added when instantiating class
	 * @param array $settings
	 */
	public function __construct($settings = array()) {
		$this->settings($settings);
	}

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
        $this->_setGetData(filter_input_array(INPUT_GET));

        $uriGetParam = $this->_parseURI();
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
     * Parse URI
     * @return string
     */
    private function _parseURI() {		
		return isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";
    }

    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
    // :: Not Found 404
    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

    /**
     * Method to be executed on a non-existing url.
     * @return void
     */
    private function _notFound() {
        echo '404: Page not found!';
    }

    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
    // :: Getters and Setters
    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

    /**
     * Set URI Parameters
     * @param array $_params
     */
    private function _setParams($_params) {
        $this->_params = $_params;
    }

    /**
     * Set Query String Parameters
     * @param array $_get
     */
    private function _setGetData($_get) {
        $this->_getData = $_get;
    }

    /**
     * Set Document Root
     * @param string $settings
     */
    public function settings($settings = array()) {
        // TODO: settings implementation
    }

    /**
     * Get URI Parameters
     * @return array
     */
    public function getParams() {
        return $this->_params;
    }

    /**
     * Get Query String Parameters
     * @return type
     */
    public function getData() {
        return $this->_getData;
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
    private function _callClassCallback($key, $params) {
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

                $this->_setParams(count($values) ? $values : NULL);
                call_user_func_array(array($class, $this->_method), array($this));

            // if not we get the values form the 2nd URI param
            } else {
                for ($i = 2; $i < count($params); $i++) {
                    $values[$count] = trim($params[$i], '/');
                    $count++;
                }

                $this->_setParams(count($values) ? $values : NULL);
                call_user_func_array(array($class, 'index'), array($this));
            }

        // if we dont have 2nd parametur we just load the class
        } else {
            for ($i = 2; $i < count($params); $i++) {
                $values[$count] = trim( $params[$i], '/' );
                $count++;
            }

            $class = new $useClass();
            $this->_setParams(count($values) ? $values : NULL);
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

        $this->_setParams(count($values) ? $values : NULL);
        call_user_func($this->_controller[$key]);
    }
}