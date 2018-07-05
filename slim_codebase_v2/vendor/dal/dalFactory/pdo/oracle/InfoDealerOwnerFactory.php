<?php
/**
 *  Framework 
 *
 * @link       
 * @copyright Copyright (c) 2017
 * @license   
 */
namespace DAL\Factory\PDO\Oracle;


/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @author Okan CIRAN
 */
class InfoDealerOwnerFactory implements \Zend\ServiceManager\FactoryInterface {
    
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $infoDealerOwner = new \DAL\PDO\Oracle\InfoDealerOwner();
        $slimApp = $serviceLocator->get('slimApp');
        $infoDealerOwner->setSlimApp($slimApp);
        return $infoDealerOwner;
    }

    

}
