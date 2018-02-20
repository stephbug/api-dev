<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Provider;

use Illuminate\Support\ServiceProvider;

class ApiDevServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    public function register(): void
    {
        $this->mergeConfig();

        $this->registerServices();
    }

    public function boot(): void
    {
        $this->publishes(
            [$this->getConfigPath() => config_path('api_dev.php')],
            'config'
        );
    }

    public function provides(): array
    {
        return array_keys($this->getServicesFromConfig());
    }

    protected function registerServices(): void
    {
        foreach ($this->getServicesFromConfig() as $abstract => $concrete) {
            $this->app->bindIf($abstract, $concrete);
        }
    }

    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'api_dev');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../config/api_dev.php';
    }

    protected function getServicesFromConfig(): array
    {
        if ($services = $this->app['config']->get('api_dev.services')) {
            return $services;
        }

        return [];
    }
}