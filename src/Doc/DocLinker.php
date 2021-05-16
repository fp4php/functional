<?php

declare(strict_types=1);

namespace Doc;

use Fp\Functional\Option\Option;

use Symfony\Component\Process\Process;

use function Fp\Collection\fold;
use function Fp\Collection\map;
use function Symfony\Component\String\u;

class DocLinker
{
    /**
     * @param string $path
     * @return array<AbstractMdHeader>
     */
    private function parseHeaders(string $path): array
    {
        $parser = new MdParser([]);
        $lines = file($path);
        $headers = map($lines, function (string $line) {
            $uLine = u($line)->trim();

            return Option::of(match (true) {
                $uLine->containsAny(MdHeader4::prefix()) => MdHeader4::fromTitle($uLine->after(MdHeader4::prefix())),
                $uLine->containsAny(MdHeader3::prefix()) => MdHeader3::fromTitle($uLine->after(MdHeader3::prefix())),
                $uLine->containsAny(MdHeader2::prefix()) => MdHeader2::fromTitle($uLine->after(MdHeader2::prefix())),
                $uLine->containsAny(MdHeader1::prefix()) => MdHeader1::fromTitle($uLine->after(MdHeader1::prefix())),
                default => null,
            });
        });

        foreach ($headers as $header) {
            $parser = $parser->combineOne($header);
        }

        return $parser->getHeaders();
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

            $headers = map($this->parseHeaders($to), function(AbstractMdHeader $header) {
                $headerTitle = $header->title;
                $headerRef = u($headerTitle)->replace(' ', '-');

                return match ($header::class) {
                    MdHeader1::class => "- [$headerTitle](#$headerRef)" . PHP_EOL,
                    MdHeader4::class => "  - [$headerTitle](#$headerRef)" . PHP_EOL,
                    default => '',
                };
            });

            $refMap = fold(
                '',
                $headers,
                fn (string $acc, string $h) => $acc . $h
            );

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
