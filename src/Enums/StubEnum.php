<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Enums;

enum StubEnum: string
{
    case INTERFACE = 'interface.stub';

    case INTERFACE_EXTENDS = 'interface.extends.stub';

    case REPOSITORY = 'repository.stub';

    case REPOSITORY_IMPLEMENTS = 'repository.implements.stub';
}
