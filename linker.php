<?php

declare(strict_types=1);

use Doc\DocLinker;

require_once 'vendor/autoload.php';

$linker = new DocLinker();
$linker->link(__DIR__ . '/src/Doc/Md/*/');

return 0;

