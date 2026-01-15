<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

$modelPath = __DIR__ . '/../../../app/Models';

if (!is_dir($modelPath)) {
    return;
}

$files = Finder::create()
    ->in($modelPath)
    ->files()
    ->name('*.php');
$models = [];

foreach ($files as $file) {
    $namespace = 'App\\Models';
    $relativePath = $file->getRelativePathname();

    $class = sprintf('\%s\%s', $namespace, strtr(substr($relativePath, 0, strrpos($relativePath, '.')), '/', '\\'));

    if (class_exists($class) && is_subclass_of($class, Model::class) && !new ReflectionClass($class)->isAbstract()) {
        $models[] = $class;
    }
}

$models = array_unique($models);

foreach ($models as $class) {
    $className = class_basename($class);

    // Test A: PSR-12 Class Naming
    test("{$class} follows PSR-12 naming convention", function () use ($className) {
        expect($className)->toMatch('/^[A-Z][a-zA-Z0-9]*$/');
    });

    // Test B: Relation Validity (Naming & Data Integrity)
    test("{$class} has valid relations data", function () use ($class) {
        try {
            $model = $class::factory()->create();
        } catch (Throwable $e) {
            $model = new $class();
        }

        scanAndCheckRelations($model);
    });
}

function scanAndCheckRelations(Model $model): void
{
    expect($model)->toBeInstanceOf(Model::class);
    $reflection = new ReflectionClass($model);

    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if ($method->class !== $model::class || $method->getNumberOfParameters() > 0) {
            continue;
        }

        $returnType = $method->getReturnType();

        if ($returnType instanceof ReflectionNamedType && is_a($returnType->getName(), Relation::class, true)) {
            $methodName = $method->getName();

            try {
                $relation = $model->{$methodName}();

                checkRelationNaming($model, $methodName, $relation);

                checkRelationData($model, $methodName, $relation);
            } catch (QueryException $e) {
                renderFriendlySqlError($model, $methodName, $e);
            } catch (Throwable $e) {
                if (!str_contains($e->getMessage(), 'TYPO DETECTED')) {
                    test()->fail("Logic Error in relation [{$methodName}]: " . $e->getMessage());
                } else {
                    throw $e;
                }
            }
        }
    }
}

function checkRelationNaming(Model $model, string $methodName, Relation $relation): void
{
    $relatedModel = $relation->getRelated();
    $targetName = class_basename($relatedModel);

    // Standard names (CamelCase)
    $singularStandard = Str::camel($targetName); // e.g., event
    $pluralStandard = Str::camel(Str::plural($targetName)); // e.g., events

    $currentLower = strtolower($methodName); // e.g., event
    $pluralLower = strtolower($pluralStandard); // e.g., events
    $singularLower = strtolower($singularStandard); // e.g., event

    if ($relation instanceof HasMany || $relation instanceof BelongsToMany) {
        if ($currentLower === $singularLower) {
            test()->fail(implode("\n", [
                '',
                '  ⚠️  NAMING CONVENTION ERROR (GRAMMAR)',
                '  ──────────────────────────────────────────',
                '  Model       : ' . class_basename($model),
                "  Function    : {$methodName}()",
                '  Relation    : ' . class_basename($relation),
                '  Analysis    : This relation returns multiple items (HasMany), but the name is Singular.',
                "  Suggestion  : Change '{$methodName}' to Plural '{$pluralStandard}'.",
                '  ──────────────────────────────────────────',
                '',
            ]));
        }
    }

    if (str_contains($currentLower, strtolower($targetName))) {
        return;
    }

    $expectedName =
        $relation instanceof HasMany || $relation instanceof BelongsToMany ? $pluralStandard : $singularStandard;

    $expectedLower = strtolower($expectedName);

    similar_text($currentLower, $expectedLower, $percent);

    if ($percent > 85 && $currentLower !== $expectedLower) {
        test()->fail(implode("\n", [
            '',
            '  ⚠️  TYPO DETECTED',
            '  ──────────────────────────────────────────',
            '  Model       : ' . class_basename($model),
            "  Function    : {$methodName}()",
            "  Target      : {$targetName}::class",
            "  Analysis    : Function name does not contain '{$targetName}', but is very similar to '{$expectedName}'.",
            "  Suggestion  : You likely made a typo. Change it to '{$expectedName}'.",
            '  ──────────────────────────────────────────',
            '',
        ]));
    }
}

function checkRelationData(Model $model, string $methodName, Relation $relation): void
{
    // Validate Instance
    expect($relation)->toBeInstanceOf(Relation::class);
    expect($relation->getRelated())->toBeInstanceOf(Model::class);

    // If model hasn't been saved (Factory failed), skip data check
    if (!$model->exists) {
        return;
    }

    // Load Data (Lazy Loading) -> $order->user
    $loadedData = $model->{$methodName};

    // Validate BelongsTo (Parent must exist if FK is set)
    if ($relation instanceof BelongsTo) {
        $foreignKey = $relation->getForeignKeyName();
        if (!empty($model->{$foreignKey})) {
            expect($loadedData)
                ->not
                ->toBeNull()
                ->toBeInstanceOf(
                    $relation->getRelated()::class,
                    "Relation data [{$methodName}] is null, even though Foreign Key [{$foreignKey}] is set.",
                );
        }
    }

    // Validate HasMany (Must be a Collection)
    if ($relation instanceof HasMany || $relation instanceof BelongsToMany || $relation instanceof HasManyThrough) {
        expect($loadedData)->toBeInstanceOf(Collection::class);
    }
}

function renderFriendlySqlError(Model $model, string $methodName, QueryException $e): void
{
    $sql = $e->getSql();
    $originalError = explode('(', $e->getMessage())[0];

    test()->fail(implode("\n", [
        '',
        '  INVALID RELATION CONFIGURATION (SQL ERROR)',
        '  ──────────────────────────────────────────',
        '  Model       : ' . class_basename($model),
        "  Relation    : {$methodName}()",
        "  Error       : {$originalError}",
        '',
        '  SQL Executed by Laravel:',
        '  ──────────────────────────────────────────',
        "  {$sql}",
        '',
        '  Analysis:',
        '  1. Check the table in the SQL above. Is the table name incorrect?',
        '  2. If table name is wrong -> Check `hasMany(Target::class)` parameter.',
        '  3. If table name is correct -> Check if the foreign key column exists in the database.',
        '  ──────────────────────────────────────────',
        '',
    ]));
}
