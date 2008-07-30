<?php
// Bootstrap
$translate = new Zend_Translate('gettext', '../data');
Zend_Registry::set('Zend_Translate', $translate);

// Inside controller action
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $helper = $this->getHelper('Translator');
        $text   = $helper->translate('foo');
        $text   = $helper->translate('foo %s', 'placeholder');

        // Short syntax
        $text   = $helper->_('foo');

        // Direct method
        $text = $this->_helper->translator('test');
        $text = $this->_helper->translator('test %s', 'placeholder');

        // Output different locale using last param
        $locale = new Zend_Locale('en_US');
        $text = $this->_helper->translator('test', $locale);
        $text = $this->_helper->translator('test', 'de');
        $text = $this->_helper->translator('test %s', 'placeholder', $locale);
    }
}