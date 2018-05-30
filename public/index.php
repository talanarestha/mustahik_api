<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Cache\Frontend\Data as FrontData;

define('ENVIRONMENT', isset($_SERVER['PLATFORM_ENV']) ? $_SERVER['PLATFORM_ENV'] : 'production');

/**
 * Read the configuration
 */
switch (ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        $config = include __DIR__ . '/../config/devel/config.php';
    break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>='))
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
        else
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
        $config = include __DIR__ . '/../config/prod/config.php';
    break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
}

try {

    $di = new FactoryDefault();

    /**
     * The URL component is used to generate all kind of urls in the application
     */
    $di->set('url', function () use ($config) {
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri($config->application->baseUri);

        return $url;
    });

    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    $di->set('db', function () use ($config) {
        try {
            return new Database(
                [
                    "host"      => $config->database->host,
                    "username"  => $config->database->username,
                    "password"  => $config->database->password,
                    "dbname"    => $config->database->name,
                    "persistent"=> true
                ]
            );
        } catch(Exception $e) {
            $log = new FileAdapter($config->log->core->path . $config->log->core->prefix . date('Ymd'));
            $log->error('Database connection error'); die('Database connection error');
        }
    });

    $di->set('config', function() use ($config){
        return $config;
    });

    $di->set('request', function() use ($config){
        return new \Phalcon\Http\Request;
    });

    // cache
    $di->set('cache', function() use ($config){
        // init cache
        $frontCache  = new FrontData(["lifetime" => 300]);
        $cache = new Redis(
            $frontCache,
            [
                "host"       => $config->cache->host,
                "port"       => $config->cache->port,
                "persistent" => true
            ]
        );
        return $cache;
    });


    /**
     * Registering an autoloader
     */
    $loader = new Loader();

    $loader->registerDirs([
        $config->application->controllersDir,
        $config->application->modelsDir
    ])->register();

    
    /**
     * Starting the application
     */
    $app = new Micro();

    /**
     * api login
     */
    $app->post('/login', function(){
        $ctrl = new AccountController;
        $ctrl->login();
    });


    /**
     * api logout
     */
    $app->get('/logout', function(){
        $ctrl = new AccountController;
        $ctrl->logout();
    });

    
    /**
     * api get list muzaki
     */
    $app->post('/muzaki/get', function(){
        $ctrl = new MuzakiController;
        $ctrl->get();
    });


    /**
     * api register personal muzaki
     */
    $app->post('/muzaki/register/personal', function(){
        $ctrl = new MuzakiController;
        $ctrl->regPersonal();
    });
    

    /**
     * api get list inbox message
     */
    $app->post('/inbox/message', function(){
        $ctrl = new InboxController;
        $ctrl->message();
    });


    /**
     * api read inbox message
     */
    $app->post('/inbox/read', function(){
        $ctrl = new InboxController;
        $ctrl->read();
    });


    /**
     * api delete inbox
     */
    $app->post('/inbox/delete', function(){
        $ctrl = new InboxController;
        $ctrl->delete();
    });


    /**
     * api get master pendidikan
     */
    $app->get('/master/pendidikan', function(){
        $ctrl = new MasterController;
        $ctrl->pendidikan();
    });


    /**
     * api get master pekerjaan
     */
    $app->get('/master/pekerjaan', function(){
        $ctrl = new MasterController;
        $ctrl->pekerjaan();
    });


    /**
     * api get master currency
     */
    $app->get('/master/currency', function(){
        $ctrl = new MasterController;
        $ctrl->currency();
    });


    /**
     * api get donation category
     */
    $app->post('/donasi/category', function(){
        $ctrl = new DonasiController;
        $ctrl->category();
    });


    /**
     * api get nomor rekening 
     */
    $app->post('/master/rekening', function(){
        $ctrl = new MasterController;
        $ctrl->rekening();
    });

    
    /**
     * api post donasi
     */
    $app->post('/donasi/submit', function(){
        $ctrl = new DonasiController;
        $ctrl->submit();
    });


    /**
     * api get donasi penerimaan
     */
    $app->post('/donasi/penerimaan', function(){
        $ctrl = new DonasiController;
        $ctrl->penerimaan();
    });


    /**
     * Not found handler
     */
    $app->notFound(function () use ($app) {
        die('404');
    });

    /**
     * Handle the request
     */
    $app->handle();
} catch (\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString();
}
