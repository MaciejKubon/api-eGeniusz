<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        //subject level
        Gate::define('create_subject_level', function(User $user) {
            return $user->role == "admin";
        });
        Gate::define('show_subject_level', function(User $user) {
            return $user->role == "admin" | $user->role == "teacher"|   $user->role == "student";
        });
        Gate::define('update_subject_level', function(User $user) {
            return $user->role == "admin";
        });
        Gate::define('delete_subject_level', function(User $user) {
            return $user->role == "admin";
        });


        //subject
        Gate::define('create_subject', function(User $user) {
            return $user->role == "admin";
        });
        Gate::define('show_subject', function(User $user) {
            return $user->role == "admin" | $user->role == "teacher"|   $user->role == "student";
        });
        Gate::define('update_subject', function(User $user) {
            return $user->role == "admin";
        });
        Gate::define('delete_subject', function(User $user) {
            return $user->role == "admin";
        });



        //lesson
        Gate::define('create_lesson', function(User $user) {
            return $user->role == "teacher";
        });
        Gate::define('show_admin_lesson', function(User $user) {
            return $user->role == "admin";
        });
        Gate::define('show_all_teacher_lesson', function(User $user) {
            return $user->role == "admin" | $user->role == "teacher" | $user->role == "student";
        });
        Gate::define('edit_lesson', function(User $user) {
            return $user->role == "admin" | $user->role == "teacher";
        });
        Gate::define('delete_lesson', function(User $user) {
            return $user->role == "admin";
        });
    }
}
