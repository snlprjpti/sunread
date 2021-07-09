<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class PrepareTesting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare:testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time_start = microtime(true);

        $this->warn("Starting preparation...");

        $this->line("<fg=green>Setting database:</> sunread_test");
        Config::set("database.connections.mysql.database", "sunread_test");

        $this->line("<fg=green>Setting environment:</> testing");
        app()['env'] = "testing";

        $this->info("Starting migration...");
        $this->call("migrate:fresh");

        $this->info("Starting seeding...");
        $this->call("db:seed");

        $time_end = round((microtime(true) - $time_start) * 1000, 2);

        $this->newLine();
        $this->line("Testing prepared in: <fg=green>{$time_end}ms</>");

        return 1;
    }
}
