<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Commands\SetupCommand;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

describe('drop statements', function () {
    beforeEach(function () {
        $this->command = new class extends SetupCommand
        {
            public function dropStatementFor(string $table): string
            {
                return $this->getDropStatement($table);
            }
        };
    });

    it('drops with cascade when the default connection uses the pgsql driver', function () {
        config()->set('database.connections.custom_connection', [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'database' => 'shield',
        ]);
        config()->set('database.default', 'custom_connection');

        expect($this->command->dropStatementFor('roles'))
            ->toBe('DROP TABLE IF EXISTS roles CASCADE');
    });

    it('drops without cascade when the default connection uses the mysql driver', function () {
        config()->set('database.connections.custom_connection', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'shield',
        ]);
        config()->set('database.default', 'custom_connection');

        expect($this->command->dropStatementFor('roles'))
            ->toBe('DROP TABLE IF EXISTS roles');
    });

    it('drops without cascade when the default connection uses the sqlite driver', function () {
        expect($this->command->dropStatementFor('roles'))
            ->toBe('DROP TABLE IF EXISTS roles');
    });
});

describe('fresh migrations ledger', function () {
    afterEach(function () {
        File::delete([config_path('filament-shield.php'), config_path('permission.php')]);
    });

    it('keeps the permission migrations ledger entry when a table drop fails', function () {
        Schema::create('migrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('migration');
            $table->integer('batch');
        });

        DB::table('migrations')->insert([
            'migration' => '2024_01_01_000000_create_permission_tables',
            'batch' => 1,
        ]);

        config()->set('permission.table_names', ['not a valid table name']);

        expect(fn () => $this->artisan('shield:setup', ['--fresh' => true])
            ->expectsConfirmation('Do you want to configure Shield for multi-tenancy?')
            ->run())
            ->toThrow(QueryException::class);

        expect(DB::table('migrations')->where('migration', 'like', '%create_permission_tables')->exists())
            ->toBeTrue();
    });
});
