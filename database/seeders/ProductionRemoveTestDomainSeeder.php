<?php

namespace Database\Seeders;

use App\Services\ProductionDomainRepairService;
use Illuminate\Database\Seeder;

/**
 * Idempotent production repair: replaces legacy artixcore.test / hello@artixcore.test in app-owned
 * CMS and settings tables. Safe to run multiple times; does not delete rows.
 *
 *   php artisan db:seed --class=ProductionRemoveTestDomainSeeder --force
 */
class ProductionRemoveTestDomainSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(ProductionDomainRepairService::class);
        $service->run($this->command);
    }
}
