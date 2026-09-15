<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Parsedown;

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
        Blade::directive('markdown', function ($expression) {
            return "<?php \$parsedown = new \\Parsedown(); \$html = \$parsedown->text({$expression}); \$html = str_replace('<pre><code', '<pre class=\"rounded bg-gray-900 text-sm p-3 overflow-x-auto\"><code', \$html); echo '<div class=\'prose dark:prose-invert max-w-none text-indigo-100\'>' . \$html . '</div>'; ?>";
        });

        // Register helper functions for role display
        $this->registerBladeHelpers();
    }

    /**
     * Register helper functions for Blade
     */
    private function registerBladeHelpers(): void
    {
        require_once app_path('Helpers/blade_helpers.php');
    }
}
