<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Concerns\HasEntityTransformers;
use Illuminate\Support\Str;

it('keeps resource manage methods isolated for duplicate basenames', function () {
    eval('namespace App\\Filament\\System\\Resources\\Procurements\\Requests; class RequestResource {}');
    eval('namespace App\\Filament\\Resources\\Procurements\\Requests; class RequestResource {}');

    $systemResource = \App\Filament\System\Resources\Procurements\Requests\RequestResource::class;
    $appResource = \App\Filament\Resources\Procurements\Requests\RequestResource::class;

    config()->set('filament-shield.policies.methods', ['view']);
    config()->set('filament-shield.policies.merge', false);
    config()->set('filament-shield.resources.manage', [
        $systemResource => ['viewAny'],
        $appResource => ['create'],
    ]);

    $transformer = new class
    {
        use HasEntityTransformers;

        public function methodsFor(string $resource): array
        {
            return $this->getDefaultPolicyMethodsOrFor($resource);
        }

        protected function format(string $case, string $value): string
        {
            return $case === 'camel' ? Str::camel($value) : $value;
        }
    };

    expect($transformer->methodsFor($systemResource))->toBe(['viewAny']);
    expect($transformer->methodsFor($appResource))->toBe(['create']);
});
