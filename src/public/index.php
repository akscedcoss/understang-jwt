<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Http\Response;
use Phalcon\Http\Response\Cookies;

$config = new Config([]);

use Phalcon\Config\ConfigFactory;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Cache\CacheFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Storage\SerializerFactory;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
require_once(BASE_PATH . "/vendor/autoload.php");

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
$loader->registerNamespaces(
    [
        'App\Components' =>  APP_PATH . '/components',
        'App\Listener' => APP_PATH . '/Listener',
        'App\Console' => APP_PATH . '/Console'
    ]
);

$loader->register();

$container = new FactoryDefault();
// even Managment Start 
$eventsManager = new EventsManager();
$eventsManager->attach(
    'notifications',
    new App\Listener\NotificationsListener()
);

$eventsManager->attach(
    'application:beforeHandleRequest',
    new App\Listener\NotificationsListener()
);

$container->set('eventsManager', $eventsManager);
// Event Managment End 
$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);

$application->setEventsManager($eventsManager);
$fileName = '../app/etc/config.php';
$factory  = new ConfigFactory();
$config = $factory->newInstance('php', $fileName);


$container->set(
    'config',
    function () {
        global  $config;
        return $config;
    }
);



$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);

$container->set(
    'cookies',
    function () {
        $response = new Response();
        $signKey  = "#1dj8$=dp?.ak//j1V$~%*0XaK\xb1\x8d\xa9\x98\x054t7w!z%C*F-Jk\x98\x05\\\x5c";

        $cookies  = new Cookies();

        $cookies->setSignKey($signKey);

        $response->setCookies($cookies);
        return $cookies;
    }
);

$container->set(
    'db',
    function () {

        return new Mysql(
            [
                'host'     => $this->get('config')->get('dbs')->get("host"),
                'username' => $this->get('config')->get('dbs')->get("username"),
                'password' => $this->get('config')->get('dbs')->get("password"),
                'dbname'   => $this->get('config')->get('dbs')->get("dbname"),
            ]
        );
    }
);

$container->set(
    'locale',
    function () {
        $trans =  (new \App\Components\Locale())->getTranslator();
        return $trans;
    }
);

$container->set(
    'cache',
    function () {
        $options = [
            
            'lifetime'          => 7200,
        ];

        $serializerFactory = new SerializerFactory();
        $adapterFactory    = new AdapterFactory(
            $serializerFactory,
            $options
        );

        $cacheFactory = new CacheFactory($adapterFactory);

        $cache = $cacheFactory->newInstance('apcu');
        return $cache;
    }
);



$container->set(
    'date',
    function () {
        return  date("M,d,Y h:i:s A");
    }
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
