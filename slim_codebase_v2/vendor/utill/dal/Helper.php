<?php
/**
 *  Framework 
 *
 * @link       
 * @copyright Copyright (c) 2017
 * @license   
 */
namespace Utill\Dal;

final class Helper {
    
    
    public static function haveRecord($result = null) { 
        print_r('---1---') ; 
           print_r($result) ; 
        print_r('---2---') ; 
                 print_r($result['resultSet'][0]['control']) ; 
         print_r('---3---') ; 
    
                print_r('---4--') ; 
        if(isset($result['resultSet'][0]['control'])) return true;
        return false;
    }
}

