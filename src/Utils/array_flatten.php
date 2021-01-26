<?php
namespace Nyxt\Utils;

function array_flatten(array $array): array {
  $result = [];
  foreach ($array as $key => $value) {
    if (is_array($value)) {
      $result = array_merge($result, array_flatten($value));
    } else {
      $result = array_merge($result, array($key => $value));
    }
  }

  return $result;
}
