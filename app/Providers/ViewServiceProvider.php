<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('components.template.sidebar', function ($view) {
            $user = Auth::user();

            if (!$user) {
                return $view->with('projects', collect());
            }

            // Ambil project yang dia punya atau yang dia jadi member-nya
            $projects = Project::query()
                ->where('owner_id', $user->id)
                ->orWhereHas('users', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $view->with('projects', $projects);
        });
    }
}
