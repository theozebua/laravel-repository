<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Enums;

enum UseStatementType: string
{
    case CLASSNAME = 'class';

    case FUNCTION = 'function';

    case CONSTANT = 'constant';
}
