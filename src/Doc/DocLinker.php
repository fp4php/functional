<?php

declare(strict_types=1);

namespace Doc;

use Fp\Collections\ArrayList;
use Fp\Collections\Seq;
use Symfony\Component\Process\Process;

class DocLinker
{
    private function after(string $needle, string $haystack): string
    {
        return substr($haystack, (int) strpos($haystack, $needle) + strlen($needle)) ?: '';
    }

    /**
     * @param string $path
     * @return Seq<AbstractMdHeader>
     */
    private function parseHeaders(string $path): Seq
    {
        /** @var list<string> $lines */
        $lines = file($path);

        return ArrayList::collect($lines)
            ->map(fn(string $line) => trim($line))
            ->map(fn(string $line) => match (true) {
                str_contains($line, MdHeader4::prefix()) => MdHeader4::fromTitle($this->after(MdHeader4::prefix(), $line)),
                str_contains($line, MdHeader3::prefix()) => MdHeader3::fromTitle($this->after(MdHeader3::prefix(), $line)),
                str_contains($line, MdHeader2::prefix()) => MdHeader2::fromTitle($this->after(MdHeader2::prefix(), $line)),
                str_contains($line, MdHeader1::prefix()) => MdHeader1::fromTitle($this->after(MdHeader1::prefix(), $line)),
                default => null,
            })
            ->filterNotNull();
    }

    public function link(string $pattern): void
    {
        $dirs = glob($pattern, GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $fromPattern = $dir . '*.md';
            $to = __DIR__ . '/../../doc/' . basename($dir) . '.md';
            $commandLine = "pandoc --from gfm --to gfm --output $to $fromPattern";

            Process::fromShellCommandline($commandLine)->run();
            $contents = file_get_contents($to);

            $refs = $this
                ->parseHeaders($to)
                ->map(fn(AbstractMdHeader $header) => match ($header::class) {
                    MdHeader1::class => sprintf("- [%s](#%s)\n", ...[
                        $header->title,
                        str_replace(' ', '-', $header->title)
                    ]),
                    MdHeader4::class => sprintf("  - [%s](#%s)\n", ...[
                        $header->title,
                        str_replace(' ', '-', $header->title)
                    ]),
                    default => '',
                })
                ->fold('')(fn (string $acc, string $h) => $acc . $h);

            file_put_contents($to, implode(PHP_EOL, [
                '# ' . basename($dir),
                '**Contents**',
                $refs,
                $contents
            ]));
        }

        echo 'doc has been built' . PHP_EOL;
    }
}
