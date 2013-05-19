<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Test
 *
 * @author Nelly
 */

namespace MVC;

class Config
{

    private static $_instance = null;
    private $_configFolder = null;
    private $_configArray = array();

    private function __construct()
    {

    }

    /**
     *
     * @return type
     */
    public function getConfigFolder()
    {
        return $this->_configFolder;
    }

    /**
     *
     * @param type $configFolder
     * @throws \Exception
     */
    public function setConfigFolder($configFolder)
    {
        if (!$configFolder) {
            throw new \Exception('Empty config folder path!');
        }

        $_configFolder = realpath($configFolder);

        if ($_configFolder !== false && is_dir($configFolder) && is_readable($configFolder)) {
            //clean old config data
            $this->_configArray = array();
            $this->_configFolder = $_configFolder . DIRECTORY_SEPARATOR;

            $ns = $this->app['namespaces'];

            if (is_array($ns)) {
                \MVC\Loader::registerNamespaces($ns);
            }
        } else {
            throw new \Exception('Config directory read error: ' . $configFolder);
        }
    }

    /**
     *
     * @param type $path
     * @throws \Exception
     */
    public function includeConfigFile($path)
    {
        if (!$path) {
            throw new \Exception;
        }

        $_file = realpath($path);

        if ($_file !== false && is_file($_file) && is_readable($_file)) {
            $_basename = explode('.php', basename($_file))[0];
            $this->_configArray[$_basename] = include $_file;
        } else {
            throw new \Exception('Config file read error: ' . $path);
        }
    }

    public function __get($name)
    {
        if (!$this->_configArray[$name]) {
            $this->includeConfigFile($this->_configFolder . $name . '.php');
        }

        if (array_key_exists($name, $this->_configArray)) {
            return $this->_configArray[$name];
        }

        return null;
    }

    /**
     *
     * @return \MVC\Config
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new \MVC\Config();
        }

        return self::$_instance;
    }
}

?>