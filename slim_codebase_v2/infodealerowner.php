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
$app->get("/pkfillServicesDdlist_infoDealerOwner/", function () use ($app ) {   
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');

    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
  
    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];
    
    $resCombobox = $BLL->fillServicesDdlist(array(
        'pk' => $pk,    
    ));
 
    $flows = array();
  //  $flows[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Lütfen Seçiniz",);
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
$app->get("/pkgetAfterSalesDetayAlisFaturalari_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAlisFaturalariWeeklyWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAlisFaturalariAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAlisFaturalariAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAlisFaturalariYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAlisFaturalariYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsemriFaturalari_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsemriFaturalariWeeklyWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsemriFaturalariAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsemriFaturalariAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsemriFaturalariYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsemriFaturalariYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetaySatisFaturalari_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetaySatisFaturalariWeeklyWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetaySatisFaturalariAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetaySatisFaturalariAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetaySatisFaturalariYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetaySatisFaturalariYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIcmalFaturalari_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIcmalFaturalariWeeklyWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIcmalFaturalariAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIcmalFaturalariAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIcmalFaturalariYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIcmalFaturalariYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcikWithoutServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcikAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcikAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcikYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcikYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcilanKapanan_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcilanKapananWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcilanKapananAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcilanKapananAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcilanKapananYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayIsEmriAcilanKapananYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardStoklar_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardStoklarWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayStoklarGrid_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDetayStoklarGridWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDashboardAracGirisSayilari_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardAracGirisSayilariWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAracGirisSayilari_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAracGirisSayilariWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAracGirisSayilariAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAracGirisSayilariAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAracGirisSayilariYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAracGirisSayilariYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardDowntime_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardDowntimeWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGridDowntime_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDetayGridDowntimeWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDashboardVerimlilik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardVerimlilikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayVerimlilikYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayVerimlilikYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardKapasite_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardKapasiteWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayKapasiteYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayKapasiteYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardEtkinlik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardEtkinlikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayEtkinlikYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayEtkinlikYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardYedekParcaTS_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardYedekParcaTSWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaTS_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaTSWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaTSAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaTSAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaTSYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaTSYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardYedekParcaYS_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardYedekParcaYSWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaYS_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaYSWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaYSAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaYSAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaYSYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayYedekParcaYSYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardAtolyeCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardAtolyeCirosuWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAtolyeCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAtolyeCirosuWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAtolyeCirosuAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAtolyeCirosuAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAtolyeCirosuYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayAtolyeCirosuYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardGarantiCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardGarantiCirosuWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardDirekSatisCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardDirekSatisCirosuWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayDirekSatisCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayDirekSatisCirosuWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayDirekSatisCirosuAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayDirekSatisCirosuAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayDirekSatisCirosuYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayDirekSatisCirosuYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardGarantiCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosu_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGarantiCirosuYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayCiro_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayCiroWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayCiroAylik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayCiroAylikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayCiroYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayCiroYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardMMCSI_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardMMCSIWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayMMCSIYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayMMCSIYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGridMMCSI_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDetayGridMMCSIWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDashboardMMCXI_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardMMCXIWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayMMCXIYillik_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayMMCXIYillikWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayGridMMCXI_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDetayGridMMCXIWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDetayBayiStok_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardIsEmriLastDataMusteri_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardIsEmriLastDataMusteri_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
 * @since 06-07-2018
 * @author Okan CIRAN
 */
$app->get("/pkgetAfterSalesDashboardIsEmriLastDataMusteri_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    
    
    $resDataGrid = $BLL->pkgetAfterSalesDashboardIsEmriLastDataMusteri(array(
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
$app->get("/pkgetAfterSalesDashboardIsEmriDataWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardFaturaData_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardFaturaDataWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardCiroYedekParca_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDashboardFaalYedekParca_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
$app->get("/pkgetAfterSalesDetayFaalYedekParca_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
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
$app->get("/pkgetAfterSalesDashboardFaalYedekParcaWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDashboardFaalYedekParcaWithServices(array(
        'url' =>  $_GET['url'],    
    ));
     $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataGrid));
});


/**
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/pkgetAfterSalesDashboardFaalYedekParcaServisDisiWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDashboardFaalYedekParcaServisDisiWithServices(array(
        'url' =>  $_GET['url'],    
    ));
     $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataGrid));
});




/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/pkgetAfterSalesDashboardFaalYagToplam_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/pkgetAfterSalesDashboardFaalYagToplamWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDashboardFaalYagToplamWithServices(array(
        'url' =>  $_GET['url'],    
    ));
     $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataGrid));
});






/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/pkgetAfterSalesDashboardFaalStokToplam_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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




/**
 * 
 * @since 13-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/pkgetAfterSalesDashboardFaalStokToplamWithServices_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    $headerParams = $app->request()->headers();

    $resDataGrid = $BLL->getAfterSalesDashboardFaalStokToplamWithServices(array(
        'url' =>  $_GET['url'],    
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
$app->get("/pkgetAfterSalesYedekParcaHedefServissiz_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
    ///////////////
   $flows = array();
                
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["TYPE"],
                $flow["OCAKMAYIS2017"], 
                $flow["OCAKMAYIS2018"],
                $flow["KARSILASTIRMA_1718_OM"],
                $flow["TOPLAM_2017"],
                $flow["Y3ILLIK_ORTALAMA"],
                $flow["AYLIK_GERCEKLESME_MIKTARI"],
                $flow["AYLIK_7ICIN_GEREKEN_MIKTAR"],
                $flow["AYLIK_8ICIN_GEREKEN_MIKTAR"],
                $flow["AYLIK_9ICIN_GEREKEN_MIKTAR"], 
                $flow["YILLIK_7ICIN_GEREKEN_MIKTAR"],  
                $flow["YILLIK_8ICIN_GEREKEN_MIKTAR"], 
                $flow["YILLIK_9ICIN_GEREKEN_MIKTAR"]
                //$flow["PARTNERCODE"]
                ); 
    }     
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
  
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray)); 
    
    //$app->response()->header("Content-Type", "application/json");
    //$app->response()->body(json_encode($resDataGrid));
});

/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/pkgetAfterSalesYedekParcaHedefServisli_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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

    $flows = array();
                
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["TYPE"],
                $flow["OCAKMAYIS2017"], 
                $flow["OCAKMAYIS2018"],
                $flow["KARSILASTIRMA_1718_OM"],
                $flow["TOPLAM_2017"],
                $flow["Y3ILLIK_ORTALAMA"],
                $flow["AYLIK_GERCEKLESME_MIKTARI"],
                $flow["AYLIK_7ICIN_GEREKEN_MIKTAR"],
                $flow["AYLIK_8ICIN_GEREKEN_MIKTAR"],
                $flow["AYLIK_9ICIN_GEREKEN_MIKTAR"], 
                $flow["YILLIK_7ICIN_GEREKEN_MIKTAR"],  
                $flow["YILLIK_8ICIN_GEREKEN_MIKTAR"], 
                $flow["YILLIK_9ICIN_GEREKEN_MIKTAR"]
                //$flow["PARTNERCODE"]
                ); 
        
        
    }     
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
  
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray)); 
    //$app->response()->header("Content-Type", "application/json");
    //$app->response()->body(json_encode($resDataGrid));
});

/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
 
$app->get("/pkgetAfterSalesYedekParcaPDFServissiz_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
     
    
    ///////////////
    $flows = array();
                
    foreach ($resDataGrid as $flow) {        
        $flows[] = array(
           $flow["SERVISID"],            
            $flow["SERVISAD"],            
            $flow["LINKPDF"], 
        
           );
    }     
      $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
  
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray)); 
    //////////////
 //   $app->response()->header("Content-Type", "application/json");
  //  $app->response()->body(json_encode($resDataGrid));
});
 

 

/**
 * @since 21-06-2018
 * @author Mustafa Zeynel Dağlı
 */
$app->get("/pkgetAfterSalesYedekParcaPDFServisli_infoDealerOwner/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('dealerOwnerBLL');
    
    $vLanguageCode = 'tr';
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
                $flow["SERVISID"],
                html_entity_decode($flow["SERVISAD"]),
                $flow["LINKPDF"]                 
                ); 
        //$flows[] = array(
        //    "SERVISID" => $flow["SERVISID"],            
        //    'SERVISAD' => $flow["SERVISAD"],            
        //    'LINKPDF' => $flow["LINKPDF"], 
        //);
    }     
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
  
    $resultArray['data'] = $flows;
    $app->response()->body(json_encode($resultArray));
    
    
    
    /*$app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataGrid));
     * *
     */
});

//detay yedek parça hedef fonk. son


$app->run();
