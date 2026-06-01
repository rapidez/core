<?php

namespace Rapidez\Core\Enums;

enum Backorders: int
{
    case No = 0;
    case Yes = 1;
    case Notify = 2;
}
