<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Fix PHP 8.5+ deprecation warning for MySQL SSL CA constant
        // Override database config to use new constant format
        if (PHP_VERSION_ID >= 80500 && class_exists(\Pdo\Mysql::class)) {
            $mysqlConnections = ['mysql', 'mariadb'];
            foreach ($mysqlConnections as $connection) {
                $config = Config::get("database.connections.{$connection}");
                if (isset($config['options']) && is_array($config['options'])) {
                    // Replace old constant with new one if present
                    $oldConstant = defined('PDO::MYSQL_ATTR_SSL_CA') ? constant('PDO::MYSQL_ATTR_SSL_CA') : null;
                    if ($oldConstant !== null && array_key_exists($oldConstant, $config['options'])) {
                        $value = $config['options'][$oldConstant];
                        unset($config['options'][$oldConstant]);
                        $config['options'][\Pdo\Mysql::ATTR_SSL_CA] = $value;
                        Config::set("database.connections.{$connection}", $config);
                    }
                }
            }
        }
    }
}
