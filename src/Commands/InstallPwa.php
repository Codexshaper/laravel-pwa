<?php

namespace CodexShaper\PWA\Commands;

use CodexShaper\PWA\PwaServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class InstallPwa extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'pwa:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel PWA';
    /**
     * The database Seeder Path.
     *
     * @var string
     */
    protected $seedersPath = __DIR__.'/../../database/seeds/';

    /**
     * Get Option.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null],
        ];
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->info('Publishing the PWA assets, database, and config files');
        // Publish only relevant resources on install
        $tags = ['pwa.tenant.migrations', 'pwa.config', 'pwa.views', 'pwa.lang'];
        $this->call('vendor:publish', ['--provider' => PwaServiceProvider::class, '--tag' => $tags]);

        $this->info('Migrating the database tables into your application');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Dumping the autoloaded files and reloading all new files');
        $composer = $this->findComposer();
        $process = Process::fromShellCommandline($composer.' dump-autoload');
        $process->setTimeout(null); // Setting timeout to null to prevent installation from stopping at a certain point in time
        $process->setWorkingDirectory(base_path())->run();

        // Load Permission routes into application's 'routes/web.php'
        $this->info('Adding Permission routes to routes/web.php');
        $routes_contents = $filesystem->get(base_path('routes/web.php'));
        if (false === strpos($routes_contents, 'PWA::routes();')) {
            $filesystem->append(
                base_path('routes/web.php'),
                "\n\PWA::routes();\n"
            );
        }
        if ($filesystem->exists(base_path('routes/tenant.php'))) {
            $this->info('Adding Permission routes to routes/tenant.php');
            $routes_contents = $filesystem->get(base_path('routes/tenant.php'));
            if (false === strpos($routes_contents, 'PWA::routes();')) {
                $filesystem->append(
                    base_path('routes/tenant.php'),
                    "\n\PWA::routes();\n"
                );
            }
        }
    }
}
