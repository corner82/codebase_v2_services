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
class InfoAfterSalesFactory implements \Zend\ServiceManager\FactoryInterface {
    
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $infoAfterSales = new \DAL\PDO\Oracle\InfoAfterSales();
        $slimApp = $serviceLocator->get('slimApp');
        $infoAfterSales->setSlimApp($slimApp);
        return $infoAfterSales;
    }

    

}
