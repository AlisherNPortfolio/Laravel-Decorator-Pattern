<?php

namespace App\Providers;

use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Repositories\PostsCacheRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PostsRepositoryInterface::class, PostsCacheRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
