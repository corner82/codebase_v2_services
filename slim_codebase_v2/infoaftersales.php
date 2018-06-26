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
$app->get("/getAfterSalesDashboardAracGirisSayilariWithServices_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardAracGirisSayilariWithServices(array(
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
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardYedekParcaTSWithServices_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardYedekParcaTSWithServices(array(
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
 * @since 14-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardYedekParcaYSWithServices_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardYedekParcaYSWithServices(array(
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
                 $flow["AY"], 
                $flow["YIL"],
                $flow["MEMNUNIYET"], 
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
                 $flow["AY"], 
                $flow["YIL"],
                $flow["MEMNUNIYET"], 
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
                $flow["AY"], 
                $flow["YIL"],
                $flow["MEMNUNIYET"],
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
                 $flow["AY"], 
                $flow["YIL"],
                $flow["MEMNUNIYET"],
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
$app->get("/getAfterSalesDashboardIsEmriDataWithServices_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardIsEmirDataWithServices(array(
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
$app->get("/getAfterSalesDashboardFaturaDataWithServices_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardFaturaDataWithServices(array(
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








//detay yedek parça sayfası fonk. baş
/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardFaalYedekParca_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardFaalYedekParca(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDetayFaalYedekParca_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDetayFaalYedekParca();
    
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
$app->get("/getAfterSalesDashboardFaalYedekParcaWithServices_infoAfterSales/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('afterSalesBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDashboardFaalYedekParcaWithServices(array(
        'url' =>  $_GET['url'],    
    ));
    $counts = 0; 
    $resDataGrid = $resDataGrid['resultSet'];  
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardFaalYagToplam_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardFaalYagToplam(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesDashboardFaalStokToplam_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesDashboardFaalStokToplam(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

//detay yedek parça sayfası fonk. son



//detay yedek parça hedef fonk. baş

/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesYedekParcaHedefServissiz_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesYedekParcaHedefServissiz(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesYedekParcaHedefServisli_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesYedekParcaHedefServisli(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesYedekParcaPDFServissiz_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesYedekParcaPDFServissiz(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
     
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
});

/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/getAfterSalesYedekParcaPDFServisli_infoAfterSales/", function () use ($app ) {
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
    
    
    $resDataGrid = $BLL->getAfterSalesYedekParcaPDFServisli(array(
        'url' =>  $_GET['url'],   
        'language_code' => $vLanguageCode,       
    ));
   
   //
   //  print_r($resDataGrid);
     $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "SERVISID" => $flow["SERVISID"],            
            'SERVISAD' => $flow["SERVISAD"],            
            'LINKPDF' => $flow["LINKPDF"], 
        );
    }     
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
  
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
    
    
    
    /*$app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
     * *
     */
});

//detay yedek parça hedef fonk. son


$app->run();
