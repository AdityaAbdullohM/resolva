<?php

namespace App\Providers;

use App\Models\Materi;
use App\Models\Problem;
use App\Models\Pengumuman;
use App\Models\Refleksi;
use App\Models\Quiz;
use App\Policies\MateriPolicy;
use App\Policies\ProblemPolicy;
use App\Policies\PengumumanPolicy;
use App\Policies\RefleksiPolicy;
use App\Policies\QuizPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Problem::class => ProblemPolicy::class,
        Materi::class => MateriPolicy::class,
        Refleksi::class => RefleksiPolicy::class,
        Quiz::class => QuizPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
