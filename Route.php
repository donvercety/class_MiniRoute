<?php
/**
 * Mini Route v2.0
 *
 * Implements function or class callbacks for a specific url.
 * It implements beautiful urls with '/' separation for parameters.
 * It can detect which parameter is a class, method or a separate function,
 * all the parameters after that are passed through the callback as array.
 *
 * @author Tommy Vercety
 */
class Route {
    
    /* Private Declaration */
    private $_uri = array(),
            $_controller = array(),
            $_method,
            $_match = 0;
    
    /**
     * Building a collection of internal URL's to look for.
     * 
     * @param string $uri
     * @param string $controller
     */
    public function add($uri, $controller = NULL)
    {
        $this->_uri[] = '/' . trim($uri, '/');
        
        if($controller != NULL)
        {
            $this->_controller[] = $controller;
        }
    }
    
    /**
     * Makes the thing run
     */
    public function submit()
    {
        // $_GET['uri'] comes form the .htaccess file
        $uriGetParam = isset($_GET['uri']) ? '/' . $_GET['uri'] : '/';
        
        // We trim the last '/' if there is any and we check that we do not trim the root '/'
        $uriGetParam = (strlen($uriGetParam) >= 2) ? rtrim($uriGetParam, "/") : $uriGetParam;

        // Making array() from $_GET['uri'].
        $uriGetParam = explode('/', $uriGetParam);

        // adding '/' to each array entry
        foreach($uriGetParam as $param) { $params[] = '/' . $param; }

        // Setting the class and method variables
        $class = $params[1];
        isset($params[2]) ? $this->_method = ltrim($params[2], "/") : NULL;

        foreach($this->_uri as $key => $value)
        {
            if(preg_match("#^$value$#", $params[1]))
            {
                $this->_match = 1;

                if(is_string( $this->_controller[$key] )) // if string then we call a php class
                {
                    $this->_callClassCallback( $key, $params );
                }
                else                                      // else we assume it's a function callback
                {                   
                    $this->_callFunctionCallback( $key, $params );
                } 
            }
        }
        if( !$this->_match ) { $this->_notFound(); }       // if there is no match, load the 404 page
    }

    /**
     * Method to be executed on a non-existing url.
     * @return void
     */
    private function _notFound()
    {
        echo '404: Page not found!';
    }

    /**
     * This method is called when we pass a string
     * as a second parameter in the add() function
     * this means that we are trying to call a php class.
     * 
     * @param  string $key      current iteration key
     * @param  array  $params   optional
     * @return void
     */
    private function _callClassCallback( $key, $params )
    {   
        $count    = 1;
        $values   = array();
        $useClass = $this->_controller[$key]; // class name to be loaded
        
        if (isset($params[2])) // if we have 2nd parametur we check if it is a method or a value
        {
            $class = new $useClass();

            if(method_exists( $class, $this->_method )) // if method we get the values form the 3rd URI param
            {
                for( $i = 3; $i < count($params); $i++ )
                {
                    $values[$count] = trim($params[$i], '/');
                    $count++;
                }
                call_user_func_array(array($class, $this->_method), array($values));
            } 
            else                                        // if not we get the values form the 2nd URI param
            {
                for( $i = 2; $i < count($params); $i++ )
                {
                    $values[$count] = trim( $params[$i], '/' );
                    $count++;
                }
                call_user_func_array(array( $class, 'index' ), array( $values ));
            }
        }
        else                   // if we dont have 2nd parametur we just load the class
        { 
            for( $i = 2; $i < count($params); $i++ )
            {
                $values[$count] = trim( $params[$i], '/' );
                $count++;
            }
            $class = new $useClass(); 
            call_user_func_array(array($class, 'index'), array( $values ));
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
    private function _callFunctionCallback( $key, $params )
    {   
        $count  = 1;
        $values = array();

        for($i = 2; $i < count($params); $i++)
        {
            $values[$count] = trim($params[$i], '/');
            $count++;
        }

        call_user_func($this->_controller[$key], $values);
    }

}
