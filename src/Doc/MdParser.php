<?php

declare(strict_types=1);

namespace Doc;

use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;

/**
 * @psalm-template T
 */
final class MdParser
{
    /**
     * @var list<AbstractMdHeader>
     */
    protected array $headers = [];

    /**
     * @param list<AbstractMdHeader> $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param list<AbstractMdHeader> $x
     * @param list<AbstractMdHeader> $y
     * @return self
     */
    protected function binaryOperation(mixed $x, mixed $y): self
    {
        $listOne =& $x;
        $listTwo =& $y;

        $merged = array_merge($listOne, $listTwo);

        return self::of($merged);
    }

    /**
     * @param list<AbstractMdHeader> $x
     * @return self
     */
    public function combine(array $x): self
    {
        return $this->binaryOperation($this->headers, $x);
    }

    /**
     * @param Option<AbstractMdHeader> $header
     * @return self
     */
    public function combineOne(Option $header): self
    {
        return $this->combine(match (true) {
            ($header instanceof Some) => [$header->get()],
            default => [],
        });
    }

    /**
     * @param list<AbstractMdHeader> $headers
     * @return self
     */
    public static function of(array $headers): self
    {
        return new self($headers);
    }

    /**
     * @return list<AbstractMdHeader>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
