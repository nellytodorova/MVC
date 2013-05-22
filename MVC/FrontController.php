<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FrontController
 *
 * @author Nelly
 */
namespace MVC;

class FrontController
{
    private static $_instance = null;
    private $ns = null;
    private $controller = null;
    private $method = null;
    private $router = null;

    private function __construct()
    {

    }

    public function getRouter() {
        return $this->router;
    }

    public function setRouter(\MVC\Routers\IRouter $router) {
        $this->router = $router;
    }


    public function dispatch()
    {
        if ($this->router == null) {
            throw new \Exception('No valid router found', 500);
        }

        $_uri = $this->router->getURI();

        $routes = \MVC\App::getInstance()->getConfig()->routes;
        $_rc = null;

        if (is_array($routes) && count($routes) > 0) {
            foreach ($routes as $k => $v) {
                if (stripos($_uri, $k) === 0 && ($_uri == $k || stripos($_uri, $k . '/') === 0) && $v['namespace']) {
                    $this->ns = $v['namespace'];
                    $_uri = substr($_uri, strlen($k) + 1);
                    $_rc = $v;
                    break;
                }
            }
        } else {
            throw new \Exception('Default route missing', 500);
        }

        if ($this->ns == null && $routes['*']['namespace']) {
            $this->ns = $routes['*']['namespace'];
            $_rc = $routes['*'];
        } else if ($this->ns == null && !$routes['*']['namespace']) {
            throw new \Exception('Default route missing', 500);
        }

        $_params = explode('/', $_uri);

        if ($_params[0]) {
            $this->controller = strtolower($_params[0]);

            // if we don't have controller and method, we don't have params
            if ($_params[1]) {
                $this->method = strtolower($_params[1]);
            } else {
                $this->method = $this->getDefaultMethod();
            }
        } else {
            $this->controller = $this->getDefaultController();
            $this->method = $this->getDefaultMethod();
        }

        if (is_array($_rc) && $_rc['controllers']) {
            if ($_rc['controllers'][$this->controller]['methods'][$this->method]) {
                $this->method = strtolower($_rc['controllers'][$this->controller]['methods'][$this->method]);
            }

            if (isset($_rc['controllers'][$this->controller]['to'])) {
                $this->controller = strtolower($_rc['controllers'][$this->controller]['to']);
            }
        }

        //TODO fixed
//        $f = $this->ns . '\\' . ucfirst($this->controller);
//        $newController = new $f();
//        $newController->{$this->method}();
    }

    public function getDefaultController()
    {
        $controller = \MVC\App::getInstance()->getConfig()->app['default_controller'];

        if ($controller) {
            return strtolower($controller);
        }

        return 'index';
    }

    public function getDefaultMethod()
    {
        $method = \MVC\App::getInstance()->getConfig()->app['default_method'];

        if ($method) {
            return strtolower($method);
        }

        return 'index';
    }

    /**
     *
     * @return \MVC\FrontController
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new \MVC\FrontController();
        }

        return self::$_instance;
    }
}
?>