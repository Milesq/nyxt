<?php
namespace Nyxt\Utils;

function is_uppercased(string $s): bool {
    return strtoupper($s) == $s;
}
