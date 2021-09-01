<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Tests\PhpBlockTestCase;

final class MapGetMethodReturnTypeProviderTest extends PhpBlockTestCase
{
    public function testReturnType(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                class FooBar
                {
                    public const TYPE_1 = "TYPE_1";
                    public const TYPE_2 = "TYPE_2";
                    public const TYPE_3 = "TYPE_3";
                    public const ALLOWED_TYPES = [
                        self::TYPE_1,
                        self::TYPE_2,
                        self::TYPE_3,
                    ];
                
                    public function __construct()
                    {
                        /** @var float $appended */
                        $appended = 123.3;
                        $pairs = \Fp\Collections\ArrayList::collect(self::ALLOWED_TYPES)
                            ->appended($appended)
                            ->map(fn($type) => [$type, rand()])
                            ->toArray();
                
                        /** @psalm-trace $some */
                        $some = \Fp\Collections\HashMap::collect($pairs)->get(self::TYPE_1);
                
                        /** @psalm-trace $option */
                        $option = \Fp\Collections\HashMap::collect($pairs)->get(123.3);
                    }
                }
            ',
            'Some<int>',
            'Option<int>',
        );
    }
}
