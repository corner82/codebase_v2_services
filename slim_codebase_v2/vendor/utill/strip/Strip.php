<?php
/**
 *  Framework 
 *
 * @link       
 * @copyright Copyright (c) 2017
 * @license   
 */

namespace Utill\Strip;

 class Strip extends AbstractStrip implements \Services\Filter\FilterChainInterface
                                              {
    
    public function __construct($params = null) {
        
        //if(empty($params))throw new Exception('strip class constructor parametre hatası');
        
        
    }
    
    public function strip($key = null) {
        $this->rewind();
        foreach ($this->stripStrategies as $key => $value) {
            if(method_exists($value, 'strip')) { 
                $value->strip($key);
            } else {
                throw new \Exception('invalid strip method for strip');
            }
        }
    }
    /**
    * Works like native "strip_tags" function but prevents
    * texts from being glued together when removing tags.
    *
    * @param string $str
    * @return string
    */
   public function ripTags($str)
   {
       // remove HTML tags
       $str = preg_replace('/<[^>]*>/', ' ', $str);

       // remove control characters 
       $str = str_replace("\r", '', $str);
       $str = str_replace("\n", ' ', $str);
       $str = str_replace("\t", ' ', $str);

       // remove multiple spaces 
       $str = trim(preg_replace('/ {2,}/', ' ', $str));

       return $str;
   }

    public function getFilterChain($name = null) {
        
    }

    public function setFilterChain(\Utill\Strip\Chain\AbstractStripChainer $filterChainer) {
        
    }

}

