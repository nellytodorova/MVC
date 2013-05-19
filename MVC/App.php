<?php
/**
 * Description of App
 *
 * @author Nelly
 */
namespace MVC;

include_once 'Loader.php';

class App
{
    private static $_instance = null;
    private $router = null;

    /**
     *
     * @var \MVC\Config
     */
    private $_config = null;

    /**
     *
     * @var \MVC\FrontController
     */
    private $_frontController = null;


    private function __construct()
    {
        \MVC\Loader::registerNamespace('MVC', dirname(__FILE__) . DIRECTORY_SEPARATOR);
        \MVC\Loader::registerAutoLoad();
        $this->_config = \MVC\Config::getInstance();
    }

    public function setConfigFolder($path)
    {
        $this->_config->setConfigFolder($path);
    }

    public function getConfigFolder()
    {
        return $this->_configFolder;
    }

    public function getRouter() {
        return $this->router;
    }

    public function setRouter($router) {
        $this->router = $router;
    }

    /**
     *
     * @return \MVC\Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    public function run()
    {
        //if config folder is not set, use default one
        if ($this->_config->getConfigFolder() == null) {
             $this->setConfigFolder('../config');
        }

        $this->_frontController = \MVC\FrontController::getInstance();

        if ($this->router instanceof \MVC\Routers\IRouter) {
            $this->_frontController->setRouter($this->router);
        } else if ($this->router == 'JsonRPCRouter') {
            //TODO fix it when the router is done
            $this->_frontController->setRouter(new \MVC\Routers\DefaultRouter());
        } else if ($this->router == 'CLIRouter') {
            //TODO fix it when the router is done
            $this->_frontController->setRouter(new \MVC\Routers\DefaultRouter());
        } else {
            $this->_frontController->setRouter(new \MVC\Routers\DefaultRouter());
        }

        $this->_frontController->dispatch();
    }

    /**
     *
     * @return \MVC\App
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new \MVC\App();
        }

        return self::$_instance;
    }
}
?>