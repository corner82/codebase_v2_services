<?php
/**
 * set max execution time to 5 minutes
 * @since 12/06/2018
 * @author Mustafa Zeynel Dağlı
 */
ini_set('max_execution_time', 300);

// test commit for branch slim2
require 'vendor/autoload.php';


use \Services\Filter\Helper\FilterFactoryNames as stripChainers;

/* $app = new \Slim\Slim(array(
  'mode' => 'development',
  'debug' => true,
  'log.enabled' => true,
  )); */

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
 * @author Mustafa Zeynel Dağlı
 * @since 24.04.2018
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
$app->add(new \Slim\Middleware\MiddlewareMQManager());


/**
 * get after sales services list for ddslick dropdown
 * @author Mustafa Zeynel Dağlı
 * @since 09-08-2016
 
 */
$app->get("/fillServicesDdlist_infoAfterSales/", function () use ($app ) {   
    $BLL = $app->getBLLManager()->get('afterSalesBLL');

    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    
    //$pk = $headerParams['X-Public']; 
    $resCombobox = $BLL->fillServicesDdlist();
 
    $flows = array();
    $flows[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Lütfen Seçiniz",);
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "text" => html_entity_decode($flow["AD"]),
            "value" => intval($flow["ID"]),
            "selected" => false,
            "description" => html_entity_decode($flow["AD"]),
            // "imageSrc"=>$flow["logo"],             
            /*"attributes" => array(                 
                    "active" => $flow["active"],   
            ),*/
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});



/**
 * 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAlisFaturalari_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAlisFaturalari(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAlisFaturalariWeeklyWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAlisFaturalariWeeklyWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAlisFaturalariAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAlisFaturalariAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAlisFaturalariAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAlisFaturalariAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAlisFaturalariYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAlisFaturalariYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAlisFaturalariYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAlisFaturalariYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsemriFaturalari_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsemriFaturalari(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsemriFaturalariWeeklyWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsemriFaturalariWeeklyWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsemriFaturalariAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsemriFaturalariAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsemriFaturalariAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsemriFaturalariAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsemriFaturalariYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsemriFaturalariYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsemriFaturalariYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsemriFaturalariYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

 /* 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetaySatisFaturalari_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetaySatisFaturalari(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/* 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetaySatisFaturalariWeeklyWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetaySatisFaturalariWeeklyWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/* 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetaySatisFaturalariAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetaySatisFaturalariAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/* 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetaySatisFaturalariAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetaySatisFaturalariAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/* 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetaySatisFaturalariYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetaySatisFaturalariYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/* 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetaySatisFaturalariYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetaySatisFaturalariYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIcmalFaturalari_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIcmalFaturalari(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIcmalFaturalariWeeklyWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIcmalFaturalariWeeklyWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIcmalFaturalariAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIcmalFaturalariAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIcmalFaturalariAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIcmalFaturalariAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIcmalFaturalariYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIcmalFaturalariYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIcmalFaturalariYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIcmalFaturalariYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 30-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 30-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcikWithoutServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcikWithoutServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcikAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcikAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcikAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcikAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcikYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcikYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 06-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcikYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcikYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 05-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcilanKapanan_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcilanKapanan(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});  

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcilanKapananWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcilanKapananWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcilanKapananAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcilanKapananAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcilanKapananAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcilanKapananAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcilanKapananYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcilanKapananYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayIsEmriAcilanKapananYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayIsEmriAcilanKapananYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardStoklar_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardStoklar(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardStoklarWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardStoklarWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * @since 12-06-2016
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayStoklarGrid_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayStoklarGrid();
    $counts = 0;
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["STOKTUTAR"]                 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/**
 * @since 11-06-2016
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayStoklarGridWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayStoklarGridWithServices();
    $counts = 0;
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["STOKTUTAR"]                 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/**
 * 
 * @since 31-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardAracGirisSayilari_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardAracGirisSayilari(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 31-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAracGirisSayilari_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAracGirisSayilari(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAracGirisSayilariWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAracGirisSayilariWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAracGirisSayilariAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAracGirisSayilariAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAracGirisSayilariAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAracGirisSayilariAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAracGirisSayilariYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAracGirisSayilariYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAracGirisSayilariYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAracGirisSayilariYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardDowntime_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardDowntime(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardDowntimeWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     

    $resDataGrid = $BLL->getAfterSalesDashboardDowntimeWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 12-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGridDowntime_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayGridDowntimeWithServices();
    $counts = 0;
    //print_r($resDataGrid);
    $resDataGrid = $resDataGrid['resultSet'];
    //print_r($resDataGrid);
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["DOWNTIME"],
                $flow["YIL"],
                $flow["TARIH"] 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/** 
 * @since 12-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGridDowntimeWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayGridDowntimeWithServices();
    $counts = 0;
    //print_r($resDataGrid);
    $resDataGrid = $resDataGrid['resultSet'];
    //print_r($resDataGrid);
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["DOWNTIME"],
                $flow["YIL"],
                $flow["TARIH"] 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardVerimlilik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardVerimlilik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardVerimlilikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     

    $resDataGrid = $BLL->getAfterSalesDashboardVerimlilikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayVerimlilikYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayVerimlilikYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayVerimlilikYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayVerimlilikYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardKapasite_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardKapasite(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardKapasiteWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     

    $resDataGrid = $BLL->getAfterSalesDashboardKapasiteWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayKapasiteYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayKapasiteYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayKapasiteYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayKapasiteYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardEtkinlik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardEtkinlik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardEtkinlikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     

    $resDataGrid = $BLL->getAfterSalesDashboardEtkinlikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayEtkinlikYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayEtkinlikYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 11-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayEtkinlikYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayEtkinlikYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardYedekParcaTS_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardYedekParcaTS(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaTS_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaTS(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaTSWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaTSWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaTSAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaTSAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaTSAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaTSAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaTSYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaTSYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaTSYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaTSYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});





/** 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardYedekParcaYS_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardYedekParcaYS(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaYS_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaYS(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaYSWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaYSWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaYSAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaYSAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaYSAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaYSAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaYSYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaYSYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayYedekParcaYSYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayYedekParcaYSYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});










/** 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardAtolyeCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardAtolyeCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardAtolyeCirosuWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardAtolyeCirosuWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAtolyeCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAtolyeCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAtolyeCirosuWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAtolyeCirosuWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAtolyeCirosuAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAtolyeCirosuAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAtolyeCirosuAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAtolyeCirosuAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAtolyeCirosuYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAtolyeCirosuYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayAtolyeCirosuYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayAtolyeCirosuYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});








/** 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardGarantiCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardGarantiCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardGarantiCirosuWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardGarantiCirosuWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});












/** 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardDirekSatisCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardDirekSatisCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});


/** 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardDirekSatisCirosuWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     

    $resDataGrid = $BLL->getAfterSalesDashboardDirekSatisCirosuWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayDirekSatisCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayDirekSatisCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayDirekSatisCirosuWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayDirekSatisCirosuWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayDirekSatisCirosuAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayDirekSatisCirosuAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayDirekSatisCirosuAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayDirekSatisCirosuAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayDirekSatisCirosuYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayDirekSatisCirosuYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 20-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayDirekSatisCirosuYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayDirekSatisCirosuYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});









/** 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardGarantiCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardGarantiCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosu_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosu(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 16-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGarantiCirosuYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayGarantiCirosuYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});











/**
 * 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayCiro_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayCiro(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
}); 

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayCiroWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayCiroWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayCiroAylik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayCiroAylik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});  

/**
 * 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayCiroAylikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayCiroAylikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayCiroYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayCiroYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});  

/** 
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayCiroYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayCiroYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardMMCSI_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardMMCSI(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardMMCSIWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     

    $resDataGrid = $BLL->getAfterSalesDashboardMMCSIWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayMMCSIYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayMMCSIYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayMMCSIYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayMMCSIYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGridMMCSI_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayGridMMCSI();
    $counts = 0;
    //print_r($resDataGrid);
    $resDataGrid = $resDataGrid['resultSet'];
    //print_r($resDataGrid);
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["DOWNTIME"],
                $flow["YIL"],
                $flow["TARIH"] 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/**
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGridMMCSIWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayGridMMCSIWithServices();
    $counts = 0;
    //print_r($resDataGrid);
    $resDataGrid = $resDataGrid['resultSet'];
    //print_r($resDataGrid);
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["DOWNTIME"],
                $flow["YIL"],
                $flow["TARIH"] 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardMMCXI_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardMMCXI(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardMMCXIWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     

    $resDataGrid = $BLL->getAfterSalesDashboardMMCXIWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayMMCXIYillik_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayMMCXIYillik(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/** 
 * @since 09-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayMMCXIYillikWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayMMCXIYillikWithServices(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGridMMCXI_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayGridMMCXI();
    $counts = 0;
    //print_r($resDataGrid);
    $resDataGrid = $resDataGrid['resultSet'];
    //print_r($resDataGrid);
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["DOWNTIME"],
                $flow["YIL"],
                $flow["TARIH"] 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/**
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayGridMMCXIWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayGridMMCXIWithServices();
    $counts = 0;
    //print_r($resDataGrid);
    $resDataGrid = $resDataGrid['resultSet'];
    //print_r($resDataGrid);
    $flows = array();
    if (isset($resDataGrid[0]['SERVISID'])) {
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["DOWNTIME"],
                $flow["YIL"],
                $flow["TARIH"] 
                );
        };
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/**
 * 
 * @since 06-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayBayiStok_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDetayBayiStok(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});   

/**
 * 
 * @since 24-04-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardIsEmriLastDataMusteri_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardIsEmriLastDataMusteri(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});




/**
 * 
 * @since 24-04-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardIsEmriData_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardIsEmirData(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 24-04-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardFaturaData_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardFaturaData(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 24-04-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardCiroYedekParca_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardCiroYedekParcaData(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    /*$resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));*/
    $app->response()->body(json_encode($resDataGrid));
});





























/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkFillGrid_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkFillGrid_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                $app, $_GET['language_code']));
    }
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }   
    
    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'language_code' => $vLanguageCode,
        'pk' => $pk,
      ));

    $resTotalRowCount = $BLL->fillGridRowTotalCount(array('language_code' => $vLanguageCode));
    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "profile_public" => $flow["profile_public"],
            "state_profile_public" => $flow["state_profile_public"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],
            "name" => $flow["name"],
            "surname" => $flow["surname"],
            "username" => $flow["username"],
            "auth_email" => $flow["auth_email"],
            "user_language" => $flow["user_language"],
            "language_name" => $flow["language_name"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "deleted" => $flow["deleted"],
            "op_user_id" => $flow["op_user_id"],
            "op_user_name" => $flow["op_user_name"],            
            "act_parent_id" => $flow["act_parent_id"],
            "auth_allow_id" => $flow["auth_allow_id"],
            "auth_alow" => $flow["auth_alow"],
            "cons_allow_id" => $flow["cons_allow_id"],
            "cons_allow" => $flow["cons_allow"],
            "consultant_id" => $flow["consultant_id"],
            "cons_name" => $flow["cons_name"],
            "cons_surname" => $flow["cons_surname"],            
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkInsert_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkInsert_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    } 
    $resDataInsert = $BLL->insert(array(
        'url' => $_GET['url'],  
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
        'pk' => $pk));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 *  * Okan CIRAN
 * @since 27-01-2016
 */
$app->get("/tempInsert_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
   // $headerParams = $app->request()->headers();
    $vsesionId = NULL;
    if (isset($_GET['sessionId'])) {
        $stripper->offsetSet('sessionId', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['sessionId']));
    } 
    $vM = NULL;
    if (isset($_GET['m'])) {
        $stripper->offsetSet('m', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['m']));
    }
    $vA = NULL;
    if (isset($_GET['a'])) {
        $stripper->offsetSet('a', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['a']));
    }
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                $app, $_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('sessionId')) {
        $vsesionId = $stripper->offsetGet('sessionId')->getFilterValue();
    }
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('m')) {
        $vM = $stripper->offsetGet('m')->getFilterValue();
    }
    if ($stripper->offsetExists('a')) {
        $vA = $stripper->offsetGet('a')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    }
    if ($vPreferredLanguage<0 ) {$vPreferredLanguage = 647 ;}
    
    $resDataInsert = $BLL->insertTemp(array(
        'url' => $_GET['url'], 
        'sessionId' =>$vsesionId,  
        'm' => $vM,
        'a' => $vA,
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
    ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 *  * Okan CIRAN
 * @since 27-01-2016
 */
$app->get("/pktempUpdate_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers();    
    if (!isset($headerParams['X-Public-Temp']))
        throw new Exception('rest api "pktempUpdate_infoUsers" end point, X-Public variable not found');
    $PkTemp = $headerParams['X-Public-Temp'];    

    $vM = NULL;
    if (isset($_GET['m'])) {
        $stripper->offsetSet('m', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['m']));
    }
    $vA = NULL;
    if (isset($_GET['a'])) {
        $stripper->offsetSet('a', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['a']));
    }
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('m')) {
        $vM = $stripper->offsetGet('m')->getFilterValue();
    }
    if ($stripper->offsetExists('a')) {
        $vA = $stripper->offsetGet('a')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    }
 
    $resDataInsert = $BLL->UpdateTemp(array(
        'url' => $_GET['url'],  
        'm' => $vM,
        'a' => $vA,
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
        'pktemp' => $PkTemp
    ));    
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkUpdate_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkUpdate_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                    $app, $_GET['language_code']));
    }
    $vId =-1;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['id']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                    $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    } 

    $resDataUpdate = $BLL->update(array(
        'url' => $_GET['url'],  
        'id' => $vId,
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
        'pk' => $pk));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataUpdate));
});

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkDeletedAct_infoUsers/", function () use ($app ) {
$stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkDeletedAct_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];   
    $vId = -1;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['id']));
    }
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    $resDataUpdate = $BLL->deletedAct(array(
        'url' => $_GET['url'],  
        'id' => $vId,       
        'pk' => $pk));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataUpdate));
});
 
/**
 *  * Okan CIRAN
 * @since 26-04-2016
 */
$app->get("/pkFillUsersListNpk_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers(); 
     if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillUsersListNpk_infoUsers" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('p_language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vNetworkKey = NULL;
    if (isset($_GET['npk'])) {
        $stripper->offsetSet('p_npk', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['npk']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('p_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('p_surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['surname']));
    }
    $vEmail = NULL;
    if (isset($_GET['email'])) {
        $stripper->offsetSet('p_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['email']));
    }
    $vCommunicationNumber = NULL;
    if (isset($_GET['communication_number'])) {
        $stripper->offsetSet('p_communication_number', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['communication_number']));
    }
    ///////////////////////////////////////////////////////////// 
    $frnetworkKey = NULL;
    $frname = NULL;
    $frsurname = NULL;
    $fremail = NULL;     
    $frcommunicationNumber = NULL;
    if (isset($_GET['filterRules'])) {
            $filterRules = trim($_GET['filterRules']);
            $jsonFilter = json_decode($filterRules, true);             
            foreach ($jsonFilter as $std) {
                if ($std['value'] != null) {
                    switch (trim($std['field'])) {
                        case 'name':                            
                            $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break;
                        case 'surname':
                            $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break;
                        case 'network_key':
                            $stripper->offsetSet('network_key', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));                            
                            break;
                        case 'email':
                            $stripper->offsetSet('email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break;
                         case 'communication_number':
                            $stripper->offsetSet('communication_number', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break; 
                        default:
                            break;
                    }
                }
            }
        }            
    //////////////////////////////////////////////////////////// 
    
    $stripper->strip();
    
    if (isset($_GET['filterRules'])) {
        $filterRules = trim($_GET['filterRules']);
        $jsonFilter = json_decode($filterRules, true);             
        $addfilterRules = NULL;
        $filterRules = NULL;
        foreach ($jsonFilter as $std) {
             if ($std['value'] != NULL) {                    
                 switch (trim($std['field'])) {
                     case 'name':                            
                            $frname = $stripper->offsetGet('name')->getFilterValue();                    
                         break;
                     case 'surname':
                            $frsurname = $stripper->offsetGet('surname')->getFilterValue();
                         break;
                     case 'network_key':
                            $frnetworkKey = $stripper->offsetGet('network_key')->getFilterValue();                            
                         break;
                     case 'email':
                             $fremail = $stripper->offsetGet('email')->getFilterValue();
                         break; 
                     case 'communication_number':
                             $frcommunicationNumber = $stripper->offsetGet('communication_number')->getFilterValue();                            
                         break;                      
                     default:
                         break;
                 } 
             }
         }
     }  
    ////////////////////////////////////////////////////////////
    if ($stripper->offsetExists('p_language_code')) {
        $vLanguageCode = $stripper->offsetGet('p_language_code')->getFilterValue();
    }     
    if ($stripper->offsetExists('p_npk')) {
        $vNetworkKey = $stripper->offsetGet('p_npk')->getFilterValue();
    } 
    if ($stripper->offsetExists('p_name')) {
        $vName = $stripper->offsetGet('p_name')->getFilterValue();
    } 
    if ($stripper->offsetExists('p_surname')) {
        $vSurname = $stripper->offsetGet('p_surname')->getFilterValue();
    } 
    if ($stripper->offsetExists('p_email')) {
        $vEmail = $stripper->offsetGet('p_email')->getFilterValue();
    } 
    if ($stripper->offsetExists('p_communication_number')) {
        $vCommunicationNumber = $stripper->offsetGet('p_communication_number')->getFilterValue();
    } 
                     
                     
    $resDataGrid = $BLL->FillUsersListNpk(array(
        'url' => $_GET['url'],  
        'language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,  
        'name' => $vName,  
        'surname' => $vSurname,  
        'email' => $vEmail,  
        'communication_number' => $vCommunicationNumber,
        'fr_name' => $frname,
        'fr_surname' =>$frsurname ,
        'fr_network_key' => $frnetworkKey,
        'fr_email' => $fremail,
        'fr_communication_number' => $frcommunicationNumber,        
        'pk'=> $pk,
    ));
    $resTotalRowCount = $BLL->FillUsersListNpkRtc(array(
        'language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,  
        'name' => $vName,  
        'surname' => $vSurname,  
        'email' => $vEmail,  
        'communication_number' => $vCommunicationNumber,
        'fr_name' => $frname,
        'fr_surname' =>$frsurname ,
        'fr_network_key' => $frnetworkKey,
        'fr_email' => $fremail,
        'fr_communication_number' => $frcommunicationNumber,        
        'pk'=> $pk,
    ));
    $counts=0;
     
    $flows = array();
    if (isset($resDataGrid[0]['name'])) { 
    foreach ($resDataGrid as $flow) {
        $flows[] = array(            
            "name" => html_entity_decode($flow["name"]),
            "surname" => html_entity_decode($flow["surname"]),
            "email" => $flow["email"],
            "iletisimadresi" => html_entity_decode($flow["iletisimadresi"]),
            "faturaadresi" => html_entity_decode($flow["faturaadresi"]),
            "communication_number1" => $flow["communication_number1"],
            "communication_number2" => $flow["communication_number2"],  
            "language_id" => $flow["language_id"],
            "language_name" => html_entity_decode($flow["language_name"]),    
            "network_key" => $flow["network_key"],  
            "attributes" => array("notroot" => true, ),
        );
        }
       $counts = $resTotalRowCount[0]['count'];
     }    

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array(); 
    $resultArray['total'] = $counts;
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});
 
/**
 *  * Okan CIRAN
 * @since 26-04-2016
 */
$app->get("/pkFillUsersInformationNpk_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers(); 
     if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillUsersInformationNpk_infoUsers" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vNetworkKey = NULL;
    if (isset($_GET['unpk'])) {
        $stripper->offsetSet('unpk', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['unpk']));
    }   
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    if ($stripper->offsetExists('unpk')) {
        $vNetworkKey = $stripper->offsetGet('unpk')->getFilterValue();
    }      
    $resDataGrid = $BLL->fillUsersInformationNpk(array(
        'url' => $_GET['url'],  
        'language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,  
        'pk'=> $pk,
    ));
     
    $flows = array();
    if (isset($resDataGrid[0]['unpk'])) { 
    foreach ($resDataGrid as $flow) {
        $flows[] = array(            
            "unpk" => $flow["unpk"],
            "registration_date" => $flow["registration_date"],
            "name" => html_entity_decode($flow["name"]),
            "surname" => html_entity_decode($flow["surname"]),
            "auth_email" => $flow["auth_email"],
            "user_language" => html_entity_decode($flow["user_language"]),
            "npk" => $flow["npk"],  
            "firm_name" => html_entity_decode($flow["firm_name"]),
            "firm_name_eng" => html_entity_decode($flow["firm_name_eng"]),
            "title" => html_entity_decode($flow["title"]),  
            "title_eng" => html_entity_decode($flow["title_eng"]),  
            "userb" => html_entity_decode($flow["userb"]), 
            "attributes" => array("notroot" => true, ), 
        );
        }
     }
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});
 
/**
 *  * Okan CIRAN
 * @since 09-09-2016
 */
$app->get("/pkInsertConsultant_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers(); 
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkInsertConsultant_infoUsers" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];   
 
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['preferred_language']));
    }
    $vRoleId = 0;
    if (isset($_GET['role_id'])) {
        $stripper->offsetSet('role_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['role_id']));
    }
    $vOsbId = 0;
    if (isset($_GET['osb_id'])) {
        $stripper->offsetSet('osb_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['osb_id']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['username']));
    }     
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                $app, $_GET['auth_email']));
    }
    $vPreferredLanguageJson = NULL;
    if (isset($_GET['preferred_language_json'])) {
        $stripper->offsetSet('preferred_language_json', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1, 
                $app, $_GET['preferred_language_json']));
    }
    $vTitle = NULL;
    if (isset($_GET['title'])) {
        $stripper->offsetSet('title', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['title']));
    }
    $vTitleEng = NULL;
    if (isset($_GET['title_eng'])) {
        $stripper->offsetSet('title_eng', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['title_eng']));
    }

    $stripper->strip();
    
    if ($stripper->offsetExists('role_id')) {
        $vRoleId = $stripper->offsetGet('role_id')->getFilterValue();
    }
    if ($stripper->offsetExists('osb_id')) {
        $vOsbId = $stripper->offsetGet('osb_id')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }    
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    }
    if ($stripper->offsetExists('title')) {
        $vTitle = $stripper->offsetGet('title')->getFilterValue();
    }
    if ($stripper->offsetExists('title_eng')) {
        $vTitleEng = $stripper->offsetGet('title_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language_json')) {
        $vPreferredLanguageJson = $stripper->offsetGet('preferred_language_json')->getFilterValue();
    }
    
    $resDataInsert = $BLL->insertConsultant(array(
        'url' => $_GET['url'],  
        'pk' => $pk,
        'role_id' => $vRoleId,
        'osb_id' => $vOsbId,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,      
        'auth_email' => $vAuthEmail,
        'title' => $vTitle,
        'title_eng' => $vTitleEng,
        'preferred_language' => $vPreferredLanguage,
        'preferred_language_json' => $vPreferredLanguageJson,        
    ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 * Okan CIRAN
 * @since 31-09-2016
 */
$app->get("/pkInsertUrgePerson_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers(); 
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkInsertConsultant_infoUsers" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];   
  
    $vRoleId = 0;
    if (isset($_GET['role_id'])) {
        $stripper->offsetSet('role_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['role_id']));
    }
    $vClusterId = 0;
    if (isset($_GET['cluster_id'])) {
        $stripper->offsetSet('cluster_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['cluster_id']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                $app, $_GET['surname']));
    } 
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                $app, $_GET['auth_email']));
    } 

    $stripper->strip();    
    if ($stripper->offsetExists('role_id')) {
        $vRoleId = $stripper->offsetGet('role_id')->getFilterValue();
    }
    if ($stripper->offsetExists('cluster_id')) {
        $vClusterId = $stripper->offsetGet('cluster_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    } 
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    }      
    
    $resDataInsert = $BLL->InsertUrgePerson(array(
        'url' => $_GET['url'],  
        'pk' => $pk,
        'role_id' => $vRoleId,
        'cluster_id' => $vClusterId,
        'name' => $vName,
        'surname' => $vSurname,            
        'auth_email' => $vAuthEmail,
        'username' => $vAuthEmail, 
    ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

 
/**
 *  * Okan CIRAN
 * @since 02-09-2016
 */
$app->get("/setPersonPassword_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL'); 
    $vM = NULL;
    if (isset($_GET['m'])) {
        $stripper->offsetSet('m', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['m']));
    }
    $vA = NULL;
    if (isset($_GET['a'])) {
        $stripper->offsetSet('a', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['a']));
    }
    $vKey = NULL;
    if (isset($_GET['key'])) {
        $stripper->offsetSet('key', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['key']));
    }    
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                $app, $_GET['password']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('m')) {
        $vM = $stripper->offsetGet('m')->getFilterValue();
    }
    if ($stripper->offsetExists('a')) {
        $vA = $stripper->offsetGet('a')->getFilterValue();
    }
    if ($stripper->offsetExists('key')) {
        $vKey = $stripper->offsetGet('key')->getFilterValue();
    }    
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
   
    $resDataInsert = $BLL->setPersonPassword(array( 
        'url' => $_GET['url'], 
        'm' => $vM,    
        'a' => $vA,    
        'key' => $vKey,        
        'password' => $vPassword,        
        ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

 /**x
 *  * Okan CIRAN
 * @since 26-01-2017
 */
$app->get("/pkUpdateConsUserConfirmAct_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkUpdateConsUserConfirmAct_infoUsers" end point, X-Public variable not found');
    }
    $Pk = $headerParams['X-Public'];      
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    } 
    $vOperationId = NULL;
    if (isset($_GET['operation_id'])) {
        $stripper->offsetSet('operation_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['operation_id']));
    } 
    $vRoleId = NULL;
    if (isset($_GET['role_id'])) {
        $stripper->offsetSet('role_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['role_id']));
    } 
    
    
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) {$vId = $stripper->offsetGet('id')->getFilterValue(); }
    if ($stripper->offsetExists('operation_id')) {$vOperationId = $stripper->offsetGet('operation_id')->getFilterValue(); }
    if ($stripper->offsetExists('role_id')) {$vRoleId = $stripper->offsetGet('role_id')->getFilterValue(); }
    $resData = $BLL->updateConsUserConfirmAct(array(                  
            'id' => $vId ,    
            'url' => $_GET['url'] ,    
            'operation_id' => $vOperationId, 
            'role_id' => $vRoleId, 
            'pk' => $Pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 

/**
 *  * Okan CIRAN
 * @since 18-01-2017
 */
$app->get("/fillUsersProfileInformationGuest_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vUnpk = NULL;
    if (isset($_GET['unpk'])) {
        $stripper->offsetSet('unpk', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                $app, $_GET['unpk']));
    } 
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    if ($stripper->offsetExists('unpk')) {
        $vUnpk = $stripper->offsetGet('unpk')->getFilterValue();
    } 
    
    $resDataGrid = $BLL->fillUsersProfileInformationGuest(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,   
        'unpk' => $vUnpk,     
    ));
     
    $flows = array();
    if (isset($resDataGrid[0]['id'])) { 
    foreach ($resDataGrid as $flow) {
        $flows[] = array(            
            //"id" => $flow["id"],
            "unpk" => $flow["unpk"],
            "picture" => $flow["picture"],
            "registration_date" => $flow["registration_date"],
            "name" => html_entity_decode($flow["name"]),            
            "surname" => html_entity_decode($flow["surname"]),            
          //  "auth_allow_id" => $flow["auth_allow_id"],            
            "auth_alow" => html_entity_decode($flow["auth_alow"]),
            "auth_email" => html_entity_decode($flow["auth_email"]),
            "preferred_language_id" => $flow["preferred_language_id"],
            "preferred_language_name" => html_entity_decode($flow["preferred_language_name"]),
            "state_active" => html_entity_decode($flow["state_active"]),   
            "profile_public" => $flow["profile_public"],            
            "state_profile_public" => html_entity_decode($flow["state_profile_public"]),            
            "attributes" => array( "active" => $flow["active"],  ), 
        );
        }
     }

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});
  
/**
 *  * Okan CIRAN
 * @since 17-01-2017
 */
$app->get("/pkFillUsersProfileInformation_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers(); 
     if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillUsersProfileInformation_infoUsers" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vUnpk = NULL;
    if (isset($_GET['unpk'])) {
        $stripper->offsetSet('unpk', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                $app, $_GET['unpk']));
    } 
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    if ($stripper->offsetExists('unpk')) {
        $vUnpk = $stripper->offsetGet('unpk')->getFilterValue();
    } 
    
    $resDataGrid = $BLL->fillUsersProfileInformation(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,   
        'unpk' => $vUnpk, 
        'pk'=> $pk, 
    ));
     
    $flows = array();
    if (isset($resDataGrid[0]['id'])) { 
    foreach ($resDataGrid as $flow) {
        $flows[] = array(            
            "id" => $flow["id"],
            "unpk" => $flow["unpk"],
            "picture" => $flow["picture"],
            "registration_date" => $flow["registration_date"],
            "name" => html_entity_decode($flow["name"]),            
            "surname" => html_entity_decode($flow["surname"]),            
            "auth_allow_id" => $flow["auth_allow_id"],            
            "auth_alow" => html_entity_decode($flow["auth_alow"]),
            "auth_email" => html_entity_decode($flow["auth_email"]),
            "preferred_language_id" => $flow["preferred_language_id"],
            "preferred_language_name" => html_entity_decode($flow["preferred_language_name"]),            
            "active" => $flow["active"],            
            "state_active" => html_entity_decode($flow["state_active"]),   
            "profile_public" => $flow["profile_public"],            
            "state_profile_public" => html_entity_decode($flow["state_profile_public"]),            
            "attributes" => array( ), 
        );
        }
     }

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});
 
/**
 *  * Okan CIRAN
 * @since 10-01-2017
 */
$app->get("/pkGetUserShortInformation_infoUsers/", function () use ($app ) {
    //$stripper = $app->getServiceManager()->get('filterChainerCustom');
    //$stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers(); 
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkGetUserShortInformation_infoUsers" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
    $resdata = $BLL->getUserShortInformation(array(  
            'pk' => $pk,
    )); 
    
    
    $flows = array();
    if (isset($resdata[0]['unpk'])) { 
    foreach ($resdata as $flow) {
        $flows[] = array(     
            "unpk" => $flow["unpk"],
            "registration_date" => $flow["registration_date"],
            "name" => html_entity_decode($flow["name"]),            
            "surname" => html_entity_decode($flow["surname"]),                        
            "auth_email" => html_entity_decode($flow["auth_email"]),
            "language_id" => $flow["language_id"],
            "language_code" => html_entity_decode($flow["language_code"]),            
            "user_picture" => $flow["user_picture"],            
            "mem_type_id" => $flow["mem_type_id"],   
            "mem_type" => html_entity_decode($flow["mem_type"]),
            "mem_logo" => html_entity_decode($flow["mem_logo"]),  
            "cons_allow" =>  $flow["cons_allow"] ,  
            
            
            "attributes" => array( ), 
        );
        }
     }
    

    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
    
  
});




$app->run();
