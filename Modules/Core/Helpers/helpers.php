<?php


use Modules\Core\Services\Core;

if (! function_exists('array_permutation')) {

    function array_permutation($input)
    {
        $results = [];

        foreach ($input as $key => $values) {
            if (empty($values)) {
                continue;
            }

            if (empty($results)) {
                foreach ($values as $value) {
                    $results[] = [$key => $value];
                }
            } else {
                $append = [];

                foreach ($results as &$result) {
                    $result[$key] = array_shift($values);

                    $copy = $result;

                    foreach ($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }

                    array_unshift($values, $result[$key]);
                }

                $results = array_merge($results, $append);
            }
        }

        return $results;
    }

    if (! function_exists('core')) {
        function core()
        {
            return app()->make(Core::class);
        }
    }

    if (! function_exists('setDotToArray')) {
        function setDotToArray($dot_syntax, &$main_array, $value, $separator = ".") 
        {
            $keys = explode($separator, $dot_syntax);
            foreach ($keys as $key) $main_array = &$main_array[$key];
            $main_array = $value;
        }
    }

    if (! function_exists('getDotToArray')) {
        function getDotToArray($dot_syntax, &$main_array, $separator = ".") 
        {
            $keys = explode($separator, $dot_syntax);
            foreach ($keys as $key) $main_array = &$main_array[$key];
            return $main_array;
        }
    }
    
}
?>
