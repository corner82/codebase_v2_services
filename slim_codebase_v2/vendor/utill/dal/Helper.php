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
          print_r( 'eeeeeeeeeee') ; 
        print_r($result['resultSet'][0] )  ; 
          print_r( 'xxxxxxxxxxxxx') ; 
        if(isset($result['resultSet'][0]['control'])) return true;
        return false;
    }
}

