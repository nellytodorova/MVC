<?php
/**
 * Description of Loader
 *
 * @author Nelly
 */
namespace MVC;

final class Loader
{
    private static $namespaces = array();

    private function __construct()
    {

    }

    public static function registerAutoLoad()
    {
        spl_autoload_register(array("\MVC\Loader", 'autoload'));
    }

    public static function autoload($class)
    {
        self::loadClass($class);
    }

    public static function loadClass($class)
    {
        //echo '<pre>' . print_r(self::$namespaces, true) . '</pre>';
        foreach (self::$namespaces as $k => $v) {
            if (strpos($class, $k) === 0) {
                $file = realpath(substr_replace(str_replace('\\', DIRECTORY_SEPARATOR, $class), $v, 0, strlen($k)) . '.php');

                if ($file && is_readable($file)) {
                    include $file;
                } else {
                    throw new \Exception('File can not be included: ' . $file);
                }
                break;
            }
        }
    }

    /**
     *
     * @param type $namespace
     * @param type $path
     * @throws \Exception
     */
    public static function registerNamespace($namespace, $path)
    {
        $namespace = trim($namespace);

        if (strlen($namespace) > 0) {
            if (!$path) {
                throw new \Exception('Invalid path!');
            }

            $_path = realpath($path);

            if ($_path && is_dir($_path) && is_readable($_path)) {
                self::$namespaces[$namespace .  '\\'] = $_path . DIRECTORY_SEPARATOR;
                //echo '<pre>' . print_r(self::$namespaces, true) . '</pre>';
            } else {
                throw new \Exception('Namespace directory read error: ' . $path);
            }
        } else {
            throw new \Exception('Invalid namespace: ' . $namespace);
        }
    }

    public static function registerNamespaces($ar)
    {
        if (is_array($ar)) {
            foreach ($ar as $k => $v) {
                self::registerNamespace($k, $v);
            }
        } else {
            throw new \Exception('Invalid namespaces');
        }
    }


    public static function getNamespaces()
    {
        return self::$namespaces;
    }

    public static function removeNamespace($namespace)
    {
        unset(self::$namespaces[$namespace]);
    }

    public static function clearNamespaces()
    {
        self::$namespaces = array();
    }
}
?>