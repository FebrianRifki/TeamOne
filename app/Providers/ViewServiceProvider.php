<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Notification;

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
        // Header: data notifikasi
        View::composer('layouts.template.app', function ($view) {
            $user = Auth::user();

            if (!$user) {
                return $view->with('notifications', collect());
            }

            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            $view->with('notifications', $notifications);
        });

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
