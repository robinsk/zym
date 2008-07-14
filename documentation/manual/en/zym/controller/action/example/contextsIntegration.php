<?php
class Default_IndexController extends Zym_Controller_Action_Abstract
{
    public $ajaxable = array(
        'index' => array('html', 'json')
    );

    public $contexts = array(
        'index' => array('xml', 'json')
    );

    public function indexAction()
    {
        // View
        $this->getView()->assign(array(
            'bar' => 'data',
            'data' => 'sdf'
        ));
    }
}
?>
/index/index/ = index.phtml
/index/index/ = index.ajax.phtml // Ajax request
/index/index/format/xml = index.xml.phtml
/index/index/format/json = Json encoded view object {'bar': 'data'}