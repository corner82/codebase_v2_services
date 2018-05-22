<?php
/**
 *  Framework 
 *
 * @link       
 * @copyright Copyright (c) 2017
 * @license   
 */
namespace DAL\Factory\PDO;


/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @author Okan CIRAN
 * created date : 15-01-2018
 */
class MobileUserMessagesFactory  implements \Zend\ServiceManager\FactoryInterface{
    
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $MobileUserMessagesFactory  = new \DAL\PDO\MobileUserMessages();  
        $slimapp = $serviceLocator->get('slimapp');            
        $MobileUserMessagesFactory -> setSlimApp($slimapp);
        return $MobileUserMessagesFactory;
      
    }
    
    
}