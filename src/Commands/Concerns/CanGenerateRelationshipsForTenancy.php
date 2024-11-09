<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Stringer;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;
use ReflectionClass;

trait CanGenerateRelationshipsForTenancy
{
    protected function generateRelationships(Panel $panel): void
    {
        Filament::setCurrentPanel($panel);

        collect($panel->getResources())
            ->values()
            ->filter(function ($resource): bool {
                return filled($this->guessResourceModelRelationshipType($resource::getModel(), Filament::getTenantModel()));
            })
            ->map(function ($resource) {
                $resource = resolve($resource);
                $tenantModel = Filament::getTenantModel();

                return [
                    'model' => $model = $resource::getModel(),
                    'modelPath' => (new ReflectionClass(resolve($model)))->getFileName(),
                    'tenantModelPath' => (new ReflectionClass(resolve($tenantModel)))->getFileName(),
                    'resource_model_method' => [
                        'name' => $resource::getTenantOwnershipRelationshipName(),
                        'relationshipName' => $this->guessResourceModelRelationshipType($model, $tenantModel),
                        'relatedModelClass' => str($tenantModel)
                            ->prepend('\\')
                            ->append('::class')
                            ->toString(),
                    ],
                    'tenant_model_method' => [
                        'name' => $resource::getTenantRelationshipName(),
                        'relationshipName' => $this->guessTenantModelRelationshipType($model, $tenantModel),
                        'relatedModelClass' => str($model)
                            ->prepend('\\')
                            ->append('::class')
                            ->toString(),
                    ],
                ];
            })
            ->each(function ($modifiedResource) {
                $resourceModelStringer = Stringer::for($modifiedResource['modelPath']);
                $tenantModelstringer = Stringer::for($modifiedResource['tenantModelPath']);

                if (! $resourceModelStringer->contains($modifiedResource['resource_model_method']['name'])) {
                    if (filled($importStatement = $this->addModelReturnTypeImportStatement($modifiedResource['resource_model_method']['relationshipName']))) {
                        if (! $resourceModelStringer->contains($importStatement)) {
                            $resourceModelStringer->append('use', $importStatement);
                        }
                    }
                    $resourceModelStringer
                        ->newLine()
                        ->indent(4)
                        ->prependBeforeLast('}', $this->methodStubGenerator(
                            $modifiedResource['resource_model_method']['name'],
                            $modifiedResource['resource_model_method']['relationshipName'],
                            $modifiedResource['resource_model_method']['relatedModelClass']
                        ))
                        ->save();
                }
                if (! $tenantModelstringer->contains($modifiedResource['tenant_model_method']['name'])) {
                    if (filled($importStatement = $this->addModelReturnTypeImportStatement($modifiedResource['tenant_model_method']['relationshipName']))) {
                        if (! $tenantModelstringer->contains($importStatement)) {
                            $tenantModelstringer->append('use', $importStatement);
                        }
                    }

                    $tenantModelstringer
                        ->newLine()
                        ->indent(4)
                        ->prependBeforeLast('}', $this->methodStubGenerator(
                            $modifiedResource['tenant_model_method']['name'],
                            $modifiedResource['tenant_model_method']['relationshipName'],
                            $modifiedResource['tenant_model_method']['relatedModelClass']
                        ))
                        ->save();
                }
            })
            ->toArray();

        $this->components->info('Relationships have been generated successfully!');
    }

    protected function getModel(string $model): ?Model
    {
        if (! class_exists($model)) {
            return null;
        }

        return app($model);
    }

    protected function getModelSchema(string $model): Builder
    {
        return $this->getModel($model)
            ->getConnection()
            ->getSchemaBuilder();
    }

    protected function getModelTable(string $model): string
    {
        return $this->getModel($model)->getTable();
    }

    protected function guessResourceModelRelationshipType(string $model, string $tenantModel): ?string
    {
        $schema = $this->getModelSchema($model);
        $table = $this->getModelTable($model);
        $columns = $schema->getColumnListing($table);
        $foreignKeyPrefix = class_basename($this->getModel($tenantModel));

        $foreignKey = str($foreignKeyPrefix)->snake()->append('_id');
        $morphType = str($foreignKeyPrefix)->snake()->append('_type');

        return match (true) {
            in_array($foreignKey, $columns) => 'belongsTo',
            /** @phpstan-ignore-next-line */
            in_array($morphType, $columns) && in_array($foreignKey, $columns) => 'morphTo',
            default => null,
        };
    }

    protected function guessTenantModelRelationshipType(string $model, string $tenantModel): ?string
    {
        $resourceModelRelationshipType = $this->guessResourceModelRelationshipType($model, $tenantModel);

        return match ($resourceModelRelationshipType) {
            'belongsTo' => 'hasMany',
            'morphTo' => 'morphMany',
            default => null,
        };
    }

    protected function methodStubGenerator(string $name, string $relationshipName, string $related): string
    {
        $returnType = str($related)->beforeLast('::')->toString();
        $stubs = [
            'belongsTo' => "        /** @return BelongsTo<{$returnType}, self> */\n    public function {$name}(): BelongsTo\n    {\n        return \$this->belongsTo({$related});\n    }",
            'morphTo' => "        /** @return MorphTo<{$returnType}, self> */\n    public function {$name}(): MorphTo\n    {\n        return \$this->morphTo();\n    }",
            'hasMany' => "        /** @return HasMany<{$returnType}, self> */\n    public function {$name}(): HasMany\n    {\n        return \$this->hasMany({$related});\n    }",
            'morphMany' => "        /** @return MorphMany<{$returnType}, self> */\n    public function {$name}(): MorphMany\n    {\n        return \$this->morphMany({$related});\n    }",
        ];

        return $stubs[$relationshipName] ?? "// No relationship defined for the given name: {$relationshipName}\n";
    }

    protected function addModelReturnTypeImportStatement(string $relationshipName): ?string
    {
        return match ($relationshipName) {
            'belongsTo' => 'use Illuminate\Database\Eloquent\Relations\BelongsTo;',
            'hasMany' => 'use Illuminate\Database\Eloquent\Relations\HasMany;',
            'morphTo' => 'use Illuminate\Database\Eloquent\Relations\MorphTo;',
            'morphMany' => 'use Illuminate\Database\Eloquent\Relations\MorphMany;',
            default => null,
        };
    }
}
