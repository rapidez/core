<?php

namespace Rapidez\Core\Enums;

enum Visibility: int
{
    case None = 1;
    case Catalog = 2;
    case Search = 3;
    case Both = 4;
}
