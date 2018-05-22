<?php
// test commit for branch slim2
require 'vendor/autoload.php';


use \Services\Filter\Helper\FilterFactoryNames as stripChainers;

/*$app = new \Slim\Slim(array(
    'mode' => 'development',
    'debug' => true,
    'log.enabled' => true,
    ));*/

$app = new \Slim\SlimExtended(array(
    'mode' => 'development',
    'debug' => true,
    'log.enabled' => true,
    'log.level' => \Slim\Log::INFO,
    'exceptions.rabbitMQ' => true,
    'exceptions.rabbitMQ.logging' => \Slim\SlimExtended::LOG_RABBITMQ_FILE,
    'exceptions.rabbitMQ.queue.name' => \Slim\SlimExtended::EXCEPTIONS_RABBITMQ_QUEUE_NAME
    ));

/**
 * "Cross-origion resource sharing" kontrolüne izin verilmesi için eklenmiştir
 * @author Okan CIRAN
 * @since 2.10.2015
 */
$res = $app->response();
$res->header('Access-Control-Allow-Origin', '*');
$res->header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");

$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareHMAC()); 
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());




   


/**
 *  *  
  *  Okan CIRAN
 * @since 11-09-2014
 */
$app->get("/fillComboBox_syslanguage/", function () use ($app ) {
    
    $BLL = $app->getBLLManager()->get('sysLanguageBLL'); 
    
    $componentType = 'bootstrap'; 
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type'] ));
    }
   
    $resCombobox = $BLL->fillComboBox ();  
 
   
    if ($componentType == 'bootstrap') {
        $menus = array();
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "id" => $menu["id"],
                "language" => $menu["language"],
                "language_eng" => $menu["language_eng"],
                "language_main_code" => $menu["language_main_code"],
            );
        }
    } else if ($componentType == 'ddslick') {
        $menus = array();
        $menus[] = array("text" => "Lütfen Bir Dil Seçiniz", "value" => -1, "selected" => true,);
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "text" => $menu["language"],
                "value" => $menu["id"],
                "selected" => false,
                "description" => $menu["language_eng"],
                "imageSrc" => ""
            );
        }
    }


    $app->response()->header("Content-Type", "application/json");
    
   if($componentType == 'ddslick'){
        $app->response()->body(json_encode($menus));
    }else if($componentType == 'bootstrap'){
        $app->response()->body(json_encode($resCombobox));
    }
  
  
    
  //$app->response()->body(json_encode($menus));
  
});

 
/**
 *  * Okan CIRAN
 * @since 05.05.2016
 */
$app->get("/pkFillLanguageDdList_syslanguage/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysLanguageBLL');
    
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkFillLanguageDdList_syslanguage" end point, X-Public variable not found');
    //$pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }
    $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
        
    $resCombobox = $BLL->fillLanguageDdList(array(                                   
                                    'language_code' => $vLanguageCode,
                        ));    

    $flows = array();
    $flows[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Lütfen Seçiniz",); 
    foreach ($resCombobox as $flow) {
        $flows[] = array(            
            "text" => $flow["name"],
            "value" =>  intval($flow["id"]),
            "selected" => false,
            "description" => $flow["name_eng"],
            "imageSrc"=>"",              
            "attributes" => array( 
                                    "active" => $flow["active"], 
                   
                ),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});
 
/**
 *  *  
  *  Okan CIRAN
 * @since 11-09-2014
 */
$app->get("/fillComboBoxTsql_syslanguage/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysLanguageBLL'); 
    
    $componentType = 'bootstrap'; 
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type'] ));
    }
    $vLanguageID = NULL;
    if (isset($_GET['lid'])) {
        $stripper->offsetSet('lid', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                                                                $app, 
                                                                $_GET['lid']));
    } 
    $stripper->strip();
    if ($stripper->offsetExists('lid')) {
        $vLanguageID = $stripper->offsetGet('lid')->getFilterValue();
    }
   
    $resCombobox = $BLL->fillComboBoxTsql (array( 
        'url' => $_GET['url'], 
        'LanguageID' => $vLanguageID, 
        )); 
 
   
    if ($componentType == 'bootstrap') {
        $menus = array();
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "id" => $menu["id"],
                "language" => html_entity_decode($menu["language"]),
                "language_eng" => html_entity_decode($menu["language_eng"]),
                "language_main_code" => $menu["language_main_code"],
                "url" => $menu["url"],
                "alan1" => html_entity_decode($menu["alan1"]),
                "alan2" => html_entity_decode($menu["alan2"]),
                "alan3" => html_entity_decode($menu["alan3"]),
                "alan4" => html_entity_decode($menu["alan4"]),
                "alan5" => html_entity_decode($menu["alan5"]),
                "alan6" => html_entity_decode($menu["alan6"]),
                "alan7" => html_entity_decode($menu["alan7"]),
                "alan8" => html_entity_decode($menu["alan8"]),
                "alan9" => html_entity_decode($menu["alan9"]),
                "alan10" => html_entity_decode($menu["alan10"]),
                "alert1" => html_entity_decode($menu["alert1"]),
                "alert2" => html_entity_decode($menu["alert2"]),
                "alert3" => html_entity_decode($menu["alert3"]),
                "alert4" => html_entity_decode($menu["alert4"]),
                "alert5" => html_entity_decode($menu["alert5"]),
                "alert6" => html_entity_decode($menu["alert6"]),
                "alert7" => html_entity_decode($menu["alert7"]), 
                "alert8" => html_entity_decode($menu["alert8"]), 
                "alert9" => html_entity_decode($menu["alert9"]), 
                "alert10" => html_entity_decode($menu["alert10"]), 
                "alert11" => html_entity_decode($menu["alert11"]), 
                
                "iletisim1" => html_entity_decode($menu["iletisim1"]),
                "iletisim2" => html_entity_decode($menu["iletisim2"]),
                "iletisim3" => html_entity_decode($menu["iletisim3"]),
                "iletisim4" => html_entity_decode($menu["iletisim4"]), 
                
                "gun1x" => html_entity_decode($menu["gun1x"]),
                "gun2x" => html_entity_decode($menu["gun2x"]),
                "gun3x" => html_entity_decode($menu["gun3x"]),
                "gun4x" => html_entity_decode($menu["gun4x"]), 
                "gun5x" => html_entity_decode($menu["gun5x"]),
                "gun6x" => html_entity_decode($menu["gun6x"]),
                "gun7x" => html_entity_decode($menu["gun7x"]),
                "gun8x" => html_entity_decode($menu["gun8x"]),  
                
                
            );
        }
    } else if ($componentType == 'ddslick') {
        $menus = array();
        $menus[] = array("text" => "Lütfen Bir Dil Seçiniz", "value" => -1, "selected" => true,);
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "text" => $menu["language"],
                "value" => $menu["id"],
                "selected" => false,
                "description" => $menu["language_eng"],
                "imageSrc" => ""
            );
        }
    }


    $app->response()->header("Content-Type", "application/json");
    
   if($componentType == 'ddslick'){
        $app->response()->body(json_encode($menus));
    }else if($componentType == 'bootstrap'){
        $app->response()->body(json_encode($resCombobox));
    }
  
  
    
  //$app->response()->body(json_encode($menus));
  
});


$app->run();