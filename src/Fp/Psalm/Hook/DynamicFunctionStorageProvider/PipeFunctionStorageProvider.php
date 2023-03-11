<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\DynamicFunctionStorageProvider;

use Fp\Collections\ArrayList;
use Fp\PsalmToolkit\PsalmApi;
use Psalm\Plugin\DynamicFunctionStorage;
use Psalm\Plugin\DynamicTemplateProvider;
use Psalm\Plugin\EventHandler\DynamicFunctionStorageProviderInterface;
use Psalm\Plugin\EventHandler\Event\DynamicFunctionStorageProviderEvent;
use Psalm\Storage\FunctionLikeParameter;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TCallable;

use function Fp\Callable\pipe;

final class PipeFunctionStorageProvider implements DynamicFunctionStorageProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            'fp\callable\pipe',
        ];
    }

    public static function getFunctionStorage(DynamicFunctionStorageProviderEvent $event): ?DynamicFunctionStorage
    {
        $templates = $event->getTemplateProvider();
        $args_count = count($event->getArgs());

        if ($args_count < 1) {
            return null;
        }

        $pipe_callables = ArrayList
            ::range(start: 1, stopExclusive: $args_count)
            ->map(fn(int $offset) => self::createABCallable($offset, $templates));

        $storage = new DynamicFunctionStorage();

        $storage->params = $pipe_callables
            ->zipWithKeys()
            ->mapN(fn(int $offset, TCallable $callable) => self::createParam(
                name: "fn_{$offset}",
                type: $callable,
            ))
            ->prepended(self::createParam(
                name: 'pipe_input',
                type: $templates->createTemplate('T1'),
            ))
            ->toList();

        $storage->return_type = $pipe_callables->lastElement()
            ->map(fn(TCallable $fn) => $fn->return_type)
            ->get();

        $storage->templates = ArrayList
            ::range(start: 1, stopExclusive: $args_count + 1)
            ->map(fn($offset) => "T{$offset}")
            ->map($templates->createTemplate(...))
            ->toList();

        return $storage;
    }

    private static function createABCallable(int $offset, DynamicTemplateProvider $templates): TCallable
    {
        return new TCallable(
            value: 'callable',
            params: [
                self::createParam(
                    name: 'input',
                    type: $templates->createTemplate("T{$offset}"),
                ),
            ],
            return_type: pipe(
                $offset,
                fn($offset) => $offset + 1,
                fn($offset) => "T{$offset}",
                $templates->createTemplate(...),
                PsalmApi::$types->asUnion(...),
            ),
        );
    }

    private static function createParam(string $name, Atomic $type): FunctionLikeParameter
    {
        return new FunctionLikeParameter(
            name: $name,
            by_ref: false,
            type: PsalmApi::$types->asUnion($type),
        );
    }
}
