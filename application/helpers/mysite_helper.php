<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('myFormatMoney')) {

    function myFormatMoney($amount, $prefix = "") {
        if ($amount == 0){
			return "-";
        } elseif ($amount != "") {
            if ($prefix <> "" and !empty($prefix)) {
                $prefix = $prefix;
            } else {
                $prefix = "";
            }

            if ($amount < 0)
                return '(' . $prefix . ' ' . number_format(abs($amount), 2, ",", ".") . ')';
            return $prefix . ' ' . number_format($amount, 2, ",", ".");
        } else {
            return "";
        }
    }

}

if (!function_exists('isInteger')) {

    function isInteger($str) {
        return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
    }

}

// add by casmadi by ivan dinata
// on 26/05/2014 11:07
if (!function_exists('repeatPrefix')) {

    function repeatPrefix($str, $loop, $prefix = '     ') {
        $temp = "";
        for ($x = 0; $x < $loop; $x++) {
            $temp .= $prefix;
        }
        return $temp . $str;
    }

}
