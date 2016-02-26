<?php

class About {

    public function index(Route $route) {

        $params = $route->getParams();
        $query  = $route->getData();

        $route->render("about", [
            "title"  => "About Page",
            "params" => isset($params) ? $params : [],
            "query"  => isset($query) ? $query : [],
        ]);

    }
}