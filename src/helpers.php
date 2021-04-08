<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

if (! function_exists ('transform_in_rules')) {
    /**
     * convert value in different methods with given rules
     *
     * @param $method
     * @param $value
     * @param $transformed array full data
     * @return int|string
     */
    function transform_in_rules($method, $value, $transformed = null)
    {
        if (strpos($method, ':') !== false) {
            [$method, $parameter] = explode(':', $method, 2);

            $need_replace_comma = false;
            if (Str::contains($parameter, '\\,')) {
                $need_replace_comma = true;
                $comma_replacement = '\\comma_replacement\\';
                $parameter = str_replace('\\,', $comma_replacement, $parameter);
            }
            $need_replace_colon = false;
            if (Str::contains($parameter, '\\:')) {
                $need_replace_colon = true;
                $colon_replacement = '\\colon_replacement\\';
                $parameter = str_replace('\\:', $colon_replacement, $parameter);
            }

            $parameters = str_getcsv($parameter);

            if ($need_replace_comma) {
                foreach ($parameters as $k => $v) {
                    $parameters [$k] = str_replace($comma_replacement, ',', $v);
                }
            }
            if ($need_replace_colon) {
                foreach ($parameters as $k => $v) {
                    $parameters [$k] = str_replace($colon_replacement, ':', $v);
                }
            }
        }

        $args_len = isset ($parameters) ? count($parameters) : 0;

        switch ($method) {
            case 'string':
                $value = "{$value}";
                break;
            case 'int':
                $value = intval($value);
                break;
            case 'trim':
                if ($args_len === 0) {
                    $value = trim($value);
                } else if ($args_len === 1) {
                    $value = trim($value, $parameters [0]);
                }
                break;
            case 'substr':
                if ($args_len === 2) {
                    $value = mb_substr($value, intval($parameters [0]), intval($parameters [1]), 'UTF-8');
                }
                break;
            case 'prepend':
                if ($args_len === 1) {
                    $value = $parameters [0] . $value;
                }
                break;
            case 'append':
                if ($args_len === 1) {
                    $value = $value . $parameters [0];
                }
                break;
            case 'split':
                if ($args_len === 1) {
                    $value = explode($parameters [0], $value, 2);
                }
                if ($args_len === 2) {
                    $value = explode($parameters [0], $value, (int)$parameters [1]);
                }
                break;
            case 'str':
                if ($args_len === 1) {
                    $value = call_user_func(array('Illuminate\Support\Str', $parameters [0]), $value);
                } else if ($args_len > 1) {
                    if ($parameters [0] === 'replaceArray') {
                        $args = [$parameters [1], Arr::except($parameters, [0, 1]), $value];
                    } else if ($parameters [0] === 'replaceFirst') {
                        $args = [$parameters [1], $parameters [$args_len - 1], $value];
                    } else {
                        $args = array_merge(Arr::except($parameters, 0), [$value]);
                    }
                    $value = call_user_func_array(array('Illuminate\Support\Str', $parameters [0]), $args);
                }
                break;
            case 'arr':
                if ($args_len === 1) {
                    $value = call_user_func(array('Illuminate\Support\Arr', $parameters [0]), $value);
                } else if ($args_len > 1) {
                    $args = array_merge([$value], Arr::except($parameters, 0));
                    $value = call_user_func_array(array('Illuminate\Support\Arr', $parameters [0]), $args);
                }
                break;
            default:
                $value = call_user_func($method, $value);
                break;
        }

        return $value;
    }
}