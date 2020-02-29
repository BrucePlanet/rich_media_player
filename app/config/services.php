<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
$di = new FactoryDefault();

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();
    $view->pageConfig = $config;
    $view->setViewsDir($config->application->viewsDir);
    $view->setPartialsDir('partials/');
    $view->registerEngines(array(
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
foreach( $config->database as $db )
{
    $di->set( $db->di_name, function () use ( $db ) {
        try {
            return new DbAdapter([
                'host' => $db['host'],
                'username' => $db['username'],
                'password' => $db['password'],
                'port' => $db['port'],
                'dbname' => $db['dbname'],
                'options' => [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
            ]);
        } catch (PDOException $e) {
            try {
                return new DbAdapter([
                    'host' => $db['fail_over_host'],
                    'username' => $db['fail_over_username'],
                    'password' => $db['fail_over_password'],
                    'port' => $db['fail_over_port'],
                    'options' => [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
                ]);
            } catch (PDOException $e) {
                mail(EMAIL_ADDR,"CRITICAL: ".$_SERVER["ENV"]." ASSET SERVER(".$_SERVER['SERVER_ADDR'].") COMPLETELY FAILED TO CONNECT TO MYSQL!",$e."\r\n\r\n\r\n".print_r($db,true),"from:noreply@".$_SERVER['SERVER_NAME']);
                echo 'Connection failed: ' . $e->getMessage();
            }
        }
    });
}

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->setShared("request", function() {
    return new Phalcon\Http\Request();
});

//Set up the flash service
$di->set('flash', function() {
    return new \Phalcon\Flash\Direct();
});
