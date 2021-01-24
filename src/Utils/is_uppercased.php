<?php
namespace Nyxt\Utils;

return function(string $s): bool {
    return strtoupper($s) == $s;
};
