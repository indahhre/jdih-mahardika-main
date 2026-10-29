<?php

namespace App\Providers;

use App\Models\Document;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.app', function ($view) {
            // Jenis Dokumen is a stable set of categories, so it's worth a nav
            // dropdown. Tahun isn't -- it grows every year and is already
            // available as a filter in the hero search and document catalogue,
            // so it doesn't get its own permanent nav entry.
            $view->with('navTypes', Document::query()->select('tipe_dokumen')->distinct()->orderBy('tipe_dokumen')->pluck('tipe_dokumen'));
        });
    }
}
