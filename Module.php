<?php

namespace Monevsdgs;

use Monevsdgs\Model\SdgscodingTable;

// use Monevsdgs\Model\HasilSasaranMDGSTable;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
//session
use Zend\Session\Container;
use Zend\View\Model\ViewModel;


class Module {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        //print_r($eventManager);
        //print_r($e->Musrembang\Model\DetailKehadiranMusrembangTable);
//        $sl = $e->getServiceLocator();
//        $this->model = new \stdClass();
//        // list loaded model
//        $this->model->kelurahan = $sl->get('Master\Model\KelurahanTable');
//        $this->model->detailusulan = $sl->get('Musrembang\Model\DetailusulankegiatanTable');

        $eventManager->getSharedManager()->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function ($e) {
            //disini predispatch
            $session = new Container('user_data');
            $uri = $_SERVER['REQUEST_URI'];
            $uri = explode("?", $uri);
            $uri = explode("/", $uri[0]);

            $i = 0;
            $status = 0;

            while ($i < sizeof($session->userakses) && $status == 0) {
                $ua = explode("/", $session->userakses[$i]->alamat_url);
                $sama = 0;
                //jika di server
                $index = 1;
                //jika simrenda/'public'
//                $index=3;
                $j = 0;
                while ($index < sizeof($uri) && $status == 0) {
                    if ($ua[$j] == $uri[$index]) {
                        $sama ++;
                    } else {
                        if ($uri[$index] == "add" && $session->userakses[$i]->isCreate == 1) {
                            $ua[$j] = "add";
                            if ($ua[$j] == $uri[$index]) {
                                $sama ++;
                            }
                        }

                        if ($uri[$index] == "edit" && $session->userakses[$i]->isUpdate == 1) {
                            $ua[$j] = "edit";
                            if ($ua[$j] == $uri[$index]) {
                                $sama ++;
                            }
                        }
                        if ($uri[$index] == "delete" && $session->userakses[$i]->isDelete == 1) {
                            $ua[$j] = "delete";
                            if ($ua[$j] == $uri[$index]) {
                                $sama ++;
                            }
                        }

                        if ($uri[$index] == "index" && $session->userakses[$i]->isRead == 1) {
                            $ua[$j] = "index";
                            if ($ua[$j] == $uri[$index]) {
                                $sama ++;
                            }
                        }
                    }

                    $index ++;
                    if ($j < sizeof($ua) - 1) {
                        $j++;
                    }
                }

                //jika simrenda/'public'
                //if ($sama == sizeof($uri) - 3) {
                if ($sama == sizeof($uri) - 2) {
                    $status = 1;
                }
                $i++;
            }
//            die;
            if ($status == 0) {
                $target = $e->getTarget();
                if ($session->id_role == 1) {
                    //return $target->redirect()->toRoute('dashboardadmin');
                } else if ($session->id_role == 2) {
//                    return $target->redirect()->toRoute('dashboardkelurahan');
                } else if ($session->id_role == 3) {
//                    return $target->redirect()->toRoute('dashboardkecamatan');
                } else if ($session->id_role == 4) {
//                    return $target->redirect()->toRoute('dashboardskpd');
                } else if ($session->id_role == 5) {
//                    return $target->redirect()->toRoute('dashboardkota');
                }
            }
        });
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                // 'Monevsdgs\Model\HasilSasaranMDGSTable' => function($sm) {
                //     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                //     $table = new HasilSasaranMDGSTable($dbAdapter);
                //     return $table;
                // }, 
                'Monevsdgs\Model\SdgscodingTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SdgscodingTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }

}
