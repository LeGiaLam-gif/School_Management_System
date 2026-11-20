<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';

        $application = parent::createApplication();
        $connectionName = $application->make('config')->get('database.default');
        $connectionConfig = $application->make('config')->get("database.connections.{$connectionName}");
        $driver = $connectionConfig['driver'] ?? null;
        $database = $connectionConfig['database'] ?? null;

        if ($driver === 'mysql' && is_string($database) && ! str_ends_with($database, '_testing')) {
            $application->make('config')->set(
                "database.connections.{$connectionName}.database",
                "{$database}_testing",
            );
        }

        $connection = $application->make('db')->connection($connectionName);
        $driver = $connection->getDriverName();
        $database = $connection->getDatabaseName();

        if (! (($driver === 'sqlite' && $database === ':memory:')
            || ($driver === 'mysql' && is_string($database) && str_ends_with($database, '_testing')))) {
            throw new RuntimeException('Tests require SQLite in memory or a dedicated MySQL database ending in _testing.');
        }

        return $application;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // View dung @vite nhung test khong can build asset.
        $this->withoutVite();
    }
}
