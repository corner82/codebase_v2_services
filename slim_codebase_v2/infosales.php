<?php

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
 * 
 * @since 07-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getKamyonSales_infoSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('salesBLL');
    
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
    
    
    $resDataGrid = $BLL->getKamyonSales(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 04-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getDealerInvoice_infoSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('salesBLL');
    
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
    
    
    $resDataGrid = $BLL->getDealerInvoice(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});



/**
 * 
 * @since 27-04-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getSalesDashboardData_infoSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('salesBLL');
    
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
    
    
    $resDataGrid = $BLL->getSalesDashboardData(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getFunnelOlcumData_infoSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('salesBLL');
    
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
    
    
    $resDataGrid = $BLL->getFunnelOlcumData(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * 
 * @since 08-05-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getFunnelBasicData_infoSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('salesBLL');
    
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
    
    
    $resDataGrid = $BLL->getFunnelBasicData(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});



$app->run();
