<?php
namespace Controllers;

class Index  extends \MVC\DefaultController;
{
    public function index()
    {

/*        $val = new \MVC\Validation();
        $val->setRule('url', 'http://test.com/')->setRule('minlength', 'http://test.com/', 5);
        $val->validate();*/

        $view = \MVC\View::getInstance();
        $view->appendToLayout('body1', 'admin.index');
        $view->appendToLayout('body2', 'index');
        $view->display('layouts.default', array('c' => array(1,2,3,4)), false);
    }
}
?>