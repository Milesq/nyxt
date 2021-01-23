<?php
return function(string $s): bool {
    return strtoupper($s) == $s;
};
