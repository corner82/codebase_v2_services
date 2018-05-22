<?php
// test commit for branch slim2
require 'vendor/autoload.php';




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
 * @author Okan CIRAN Ğ
 * @since 05.01.2016
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
 *  * OKAN CIRAN
 * @since 05-01-2016
 */
$app->get("/pkSessionControl_blLoginLogout/", function () use ($app ) {
 
    $BLL = $app->getBLLManager()->get('blLoginLogoutBLL'); 
    $headerParams = $app->request()->headers(); 
    $pk = $headerParams['X-Public'];
   
    $resDataMenu = $BLL->pkSessionControl(array('pk'=>$pk));
   // print_r($resDataMenu);
   
 
    $menus = array();
    foreach ($resDataMenu as $menu){
        $menus[]  = array(
            "id" => $menu["id"],
            "name" => $menu["name"],
             "data" => $menu["data"],
             "lifetime" => $menu["lifetime"],
             "c_date" => $menu["c_date"],
             "modified" => $menu["modified"],
             "public_key" => $menu["public_key"],
             "u_name" => $menu["u_name"],
             "u_surname" => $menu["u_surname"],
             "username" => $menu["username"],
           
             
        );
    }
    
    $app->response()->header("Content-Type", "application/json");
    
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($menus));
  
});
 

$app->run();