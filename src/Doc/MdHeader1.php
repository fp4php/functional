<?php

declare(strict_types=1);

namespace Doc;

class MdHeader1 extends AbstractMdHeader
{
    public static function prefix(): string
    {
        return '# ';
    }
}
