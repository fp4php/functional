<?php

declare(strict_types=1);

namespace Doc;

use Fp\Collections\ArrayList;
use Fp\Collections\Seq;
use Fp\Functional\Option\Option;
use Symfony\Component\Process\Process;

use function Fp\Collection\map;

class DocLinker
{
    /**
     * @param string $path
     * @return Seq<AbstractMdHeader>
     */
    private function parseHeaders(string $path): Seq
    {
        $parser = new MdParser([]);
        $lines = file($path);
        $headers = map($lines, function (string $line) {
            $uLine = trim($line);
            $after = fn(string $prefix): string => substr($uLine, (int) strpos($uLine, $prefix) + strlen($prefix));

            return Option::fromNullable(match (true) {
                str_contains($uLine, MdHeader4::prefix()) => MdHeader4::fromTitle($after(MdHeader4::prefix())),
                str_contains($uLine, MdHeader3::prefix()) => MdHeader3::fromTitle($after(MdHeader3::prefix())),
                str_contains($uLine, MdHeader2::prefix()) => MdHeader2::fromTitle($after(MdHeader2::prefix())),
                str_contains($uLine, MdHeader1::prefix()) => MdHeader1::fromTitle($after(MdHeader1::prefix())),
                default => null,
            });
        });

        foreach ($headers as $header) {
            $parser = $parser->combineOne($header);
        }

        return ArrayList::collect($parser->getHeaders());
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

            $refMap = $this
                ->parseHeaders($to)
                ->map(function(AbstractMdHeader $header) {
                    $headerTitle = $header->title;
                    $headerRef = str_replace(' ', '-', $headerTitle);

                    return match ($header::class) {
                        MdHeader1::class => "- [$headerTitle](#$headerRef)" . PHP_EOL,
                        MdHeader4::class => "  - [$headerTitle](#$headerRef)" . PHP_EOL,
                        default => '',
                    };
                })
                ->fold('', fn (string $acc, string $h) => $acc . $h);

            file_put_contents($to, implode(PHP_EOL, [
                '# ' . basename($dir),
                '**Contents**',
                $refMap,
                $contents
            ]));
        }

        echo 'doc has been built' . PHP_EOL;
    }

}
