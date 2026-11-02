<?php

namespace App\Console\Commands;

use App\Services\StoreAutomation\StoreAutomationRunner;
use Illuminate\Console\Command;

class RunStoreAutomationsCommand extends Command
{
    protected $signature = 'store:automations';

    protected $description = 'Run abandoned cart, post-purchase, reorder, and low-stock automations';

    public function handle(StoreAutomationRunner $runner): int
    {
        $counts = $runner->runAll();
        $this->info('Store automations: '.json_encode($counts));

        return self::SUCCESS;
    }
}
