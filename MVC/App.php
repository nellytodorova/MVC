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
    private $_dbConnections = array();

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

    public function getDBConnection($connection = 'default')
    {
        if (!$connection) {
            throw new \Exception('No connection identifier provided', 500);
        }

        if ($this->_dbConnections[$connection]) {
            return $this->_dbConnections[$connection];
        }

        $_cnf = $this->getConfig()->database;

        if (!$_cnf[$connection]) {
            throw new \Exception('No valid connection identificator provided', 500);
        }

        $dbh = new \PDO($_cnf[$connection]['connection_uri'], $_cnf[$connection]['username'],
                $_cnf[$connection]['pass'], $_cnf[$connection]['pdo_options']);

        $this->_dbConnections[$connection] = $dbh;

        return $dbh;
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