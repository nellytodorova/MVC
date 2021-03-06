<?php
namespace MVC;

include_once 'Loader.php';

class App
{
    private static $_instance = null;
    private $router = null;
    private $_dbConnections = array();
    private $_session = null;

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
        set_exception_handler(array($this, '_exceptionHandler'));
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
            $this->_frontController->setRouter(new \MVC\Routers\JsonRPCRouter());
        } else if ($this->router == 'CLIRouter') {
            //TODO fix it when the router is done
            $this->_frontController->setRouter(new \MVC\Routers\DefaultRouter());
        } else {
            $this->_frontController->setRouter(new \MVC\Routers\DefaultRouter());
        }

        $_sess = $this->_config->app['session'];

        if ($_sess['autostart']) {
            if ($_sess['type'] == 'native') {
                $_s = new \MVC\Session\NativeSession($_sess['name'], $_sess['lifetime'], $_sess['path'], $_sess['domain'], $_sess['secure']);
            } else if ($_sess['type'] == 'database') {
                $_s = new \MVC\Session\DBSession($_sess['dbConnection'], $_sess['name'], $_sess['dbTable'], $_sess['lifetime'], $_sess['path'], $_sess['domain'], $_sess['secure']);
            } else {
                throw new \Exception('No valid session', 500);
            }

            $this->setSession($_s);
        }

        $this->_frontController->dispatch();
    }

    public function getSession()
    {
        return $this->_session;
    }

    public function setSession(\MVC\Session\ISession $session)
    {
        $this->_session = $session;
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

    public function _exceptionHandler(\Exception $ex)
    {
        if ($this->_config && $this->_config->app['displayExceptions'] == true) {
            echo '<pre>' . print_r($ex, true) . '</pre>';
        } else {
            $this->displayError($ex->getCode());
        }
    }

    public function displayError($error)
    {
        try {
            $view = \MVC\View::getInstance();
            $view->display('errors.' . $error);
        } catch (\Exception $exc) {
            \MVC\Common::headerStatus($error);
            echo '<h1>' . $error . '</h1>';
            exit;
        }
    }

    public function __destruct()
    {
        if ($this->_session != null) {
            $this->_session->saveSession();
        }
    }
}
?>