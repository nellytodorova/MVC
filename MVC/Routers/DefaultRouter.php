<?php
/**
 * Description of DefaultRouter
 *
 * @author Nelly
 */
namespace MVC\Routers;

class DefaultRouter implements \MVC\Routers\IRouter
{
    public function getURI()
    {
        return substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']) + 1);
    }
}
?>