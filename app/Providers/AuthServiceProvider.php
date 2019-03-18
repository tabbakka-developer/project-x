<?php

namespace App\Providers;

use App\Event;
use App\Policies\EventPolicy;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
	    Event::class => EventPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('user', function (User $user) {
        	return $user->hasRole('user');
        });

        Gate::define('admin', function (User $user) {
        	return $user->hasRole('admin');
        });
    }
}
