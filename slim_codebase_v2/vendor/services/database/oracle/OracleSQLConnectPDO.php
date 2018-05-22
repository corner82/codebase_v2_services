<?php
/**
 * OSB İMALAT Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSB İMALAT (http://www.uretimosb.com)
 * @license 
 */

namespace Services\Database\Oracle;

 
/**
 * service manager layer for oracle database connection
 * @author Mustafa Zeynel Dağlı
 */
class OracleSQLConnectPDO implements \Zend\ServiceManager\FactoryInterface {
    
    /**
     * service ceration via factory on zend service manager
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return boolean|\PDO
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        try {
            $pdo = new \PDO('oci:dbname=192.168.10.11/EBA;charset=UTF8;', 
                    'eba', 
                    'eba123');
            return $pdo;
            //return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        } 


    }

}
