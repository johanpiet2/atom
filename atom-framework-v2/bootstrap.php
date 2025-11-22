<?php

/**
 * Unified Framework v2 Bootstrap.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */

// Prevent direct access
if (PHP_SAPI === 'cli' 
    && isset($_SERVER['SCRIPT_FILENAME']) 
    && basename((string) $_SERVER['SCRIPT_FILENAME']) === 'bootstrap.php'
) {
    echo "ERROR: This file must be included from within AtoM.\n";
    return;
}

// 1. Load Composer autoloader
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new RuntimeException('Framework v2 vendor/autoload.php not found. Run: composer install');
}

require_once __DIR__ . '/vendor/autoload.php';

// 2. Initialize Laravel Database Capsule
if (!class_exists('\Illuminate\Database\Capsule\Manager')) {
    throw new RuntimeException('Laravel Database component not loaded');
}

// Only initialize if not already done
if (!defined('ATOM_FRAMEWORK_V2_DB_INITIALIZED')) {
    try {
        // Load database credentials from config/config.php
        $configPhp = sfConfig::get('sf_config_dir').'/config.php';
        
        if (!file_exists($configPhp)) {
            throw new RuntimeException('config/config.php not found');
        }
        
        // Load the config array
        $config = include($configPhp);
        
        // Extract from nested structure: ['all']['propel']['param']
        if (!isset($config['all']['propel']['param'])) {
            throw new RuntimeException('Database configuration not found in config.php');
        }
        
        $dbConfig = $config['all']['propel']['param'];
        
        // Parse DSN to extract database name and host
        $dsn = $dbConfig['dsn'] ?? '';
        
        // Extract database name
        if (preg_match('/mysql:dbname=([^;]+)/', $dsn, $matches)) {
            $database = $matches[1];
        } else {
            throw new RuntimeException('Could not parse database name from DSN');
        }
        
        // Extract host (if present in DSN)
        if (preg_match('/host=([^;]+)/', $dsn, $matches)) {
            $hostname = $matches[1];
        } else {
            $hostname = 'localhost';
        }
        
        // Extract port (if present in DSN)
        if (preg_match('/port=([^;]+)/', $dsn, $matches)) {
            $port = $matches[1];
        } else {
            $port = '3306';
        }
        
        $username = $dbConfig['username'] ?? '';
        $password = $dbConfig['password'] ?? '';
        
        // Validate required fields
        if (empty($database)) {
            throw new RuntimeException('Database name not found');
        }
        
        if (empty($username)) {
            throw new RuntimeException('Database username not found');
        }
        
        // Create new Capsule instance
        $capsule = new \Illuminate\Database\Capsule\Manager();
        
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $hostname,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ]);
        
        // Make this Capsule instance available globally
        $capsule->setAsGlobal();
        
        // Setup the Eloquent ORM
        $capsule->bootEloquent();
        
        // Mark as initialized
        define('ATOM_FRAMEWORK_V2_DB_INITIALIZED', true);
        
    } catch (Exception $e) {
        error_log('[Framework v2] Database initialization failed: ' . $e->getMessage());
        // Don't throw - allow AtoM to continue without Framework v2
    }
}

// 3. Register extension namespaces with Composer autoloader
$loader = require __DIR__ . '/vendor/autoload.php';

$loader->addPsr4('AtomExtensions\\Extensions\\', __DIR__ . '/src/Extensions/');
$loader->addPsr4('AtomExtensions\\Reports\\', __DIR__ . '/src/Reports/');
$loader->addPsr4('AtomExtensions\\Repositories\\', __DIR__ . '/src/Repositories/');
$loader->addPsr4('AtomExtensions\\Services\\', __DIR__ . '/src/Services/');
$loader->addPsr4('AtomExtensions\\Forms\\', __DIR__ . '/src/Forms/');
$loader->addPsr4('AtomExtensions\\Core\\', __DIR__ . '/src/Core/');

// 4. Mark as loaded
if (!defined('ATOM_FRAMEWORK_V2_LOADED')) {
    define('ATOM_FRAMEWORK_V2_LOADED', true);
}
