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

    // setup security service
    $di->set('security', function(){
        $security = new Phalcon\Security();
        $security->setWorkFactor(12); //Set the password hashing factor to 12 rounds
        return $security;
    }, true);

    $di->set('mustahik_helper', function(){
        require_once __DIR__ .'/../library/MustahikHelper.php';
        return new MustahikHelper;
    }, true);

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

    $app->post('/auth/login', function(){
        $ctrl = new AccountController;
        $ctrl->login();
    });

    $app->post('/auth/password', function(){
        $ctrl = new AccountController;
        $ctrl->password();
    });

    $app->post('/pengajuan', function(){
        $ctrl = new PengajuanController;
        $ctrl->index();
    });

    $app->post('/pengajuan/get', function(){
        $ctrl = new PengajuanController;
        $ctrl->get();
    });

    $app->post('/pengajuan/search', function(){
        $ctrl = new PengajuanController;
        $ctrl->search();
    });

    $app->post('/survey/get', function(){
        $ctrl = new SurveyController;
        $ctrl->get();
    });

    $app->post('/survey/save', function(){
        $ctrl = new SurveyController;
        $ctrl->save();
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
