<?php

namespace Monevsdgs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class DashboardController extends AbstractActionController {

    private $model;

    public function onDispatch(MvcEvent $e) {
        $sl = $this->getServiceLocator();
        $this->model = new \stdClass();
        // list loaded model
        return parent::onDispatch($e);
    }
     public function indexAction() {
		$this->layout()->setVariable("title_page", 'Dashboard SDGS');
        $this->layout()->setVariable("active_page", '');
        $this->layout()->setVariable("breadcrumbs", array(
                'SDGS' => '#', // No link
                'Dashboard' => '', // Link
                ));
        return new ViewModel(array(
            
        ));
    }
}
?>