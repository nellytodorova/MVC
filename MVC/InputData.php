<?php
namespace MVC;

class InputData
{
    private static $_instance = null;
    private $_get = array();
    private $_post = array();
    private $_cookies = array();

    public function __construct() {
        $this->_cookies = $_COOKIE;
    }

    /**
     *
     * @param type $ar
     */
    public function setPost($ar)
    {
        if (is_array($ar)) {
            $this->_post = $ar;
        }
    }

    /**
     *
     * @param type $ar
     */
    public function setGet($ar)
    {
        if (is_array($ar)) {
            $this->_get = $ar;
        }
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function hasGet($id)
    {
        return array_key_exists($id, $this->_get);
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function hasCookies($name)
    {
        return array_key_exists($name , $this->_cookies);
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function hasPost($name)
    {
        return array_key_exists($name , $this->_post);
    }

    /**
     *
     * @param type $id
     * @param type $normalize
     * @param type $default
     * @return type
     */
    public function get($id, $normalize = null, $default = null )
    {
        if ($this->hasGet($id)) {
            if ($normalize != null) {
                return \MVC\Common::normalize($this->_get[$id], $normalize);
            }

            return $this->_get[$id];
        }

        return $default;
    }

    /**
     *
     * @param type $name
     * @param type $normalize
     * @param type $default
     * @return type
     */
    public function post($name, $normalize = null, $default = null )
    {
        if ($this->hasPost($name)) {
            if ($normalize != null) {
                return \MVC\Common::normalize($this->_post[$name], $normalize);
            }

            return $this->_post[$name];
        }

        return $default;
    }

    /**
     *
     * @param type $name
     * @param type $normalize
     * @param type $default
     * @return type
     */
    public function cookies($name, $normalize = null, $default = null )
    {
        if ($this->hasCookies($name)) {
            if ($normalize != null) {
                return \MVC\Common::normalize($this->_cookies[$name], $normalize);
            }

            return $this->_cookies[$name];
        }

        return $default;
    }

    /**
     *
     * @return \MVC\InputData
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new \MVC\InputData();
        }

        return self::$_instance;
    }
}
?>