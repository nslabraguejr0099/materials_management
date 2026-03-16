<?php

namespace Modules;

class Helpers
{
    public static function checkIfEmpty ( $data ) 
    {
        if ( is_null($data) ) {
            return true;
        } else {
            if ( is_array($data) ) {
                if ( empty($data) )
                    return true;
            } else {
                if ( $data == '' )
                    return true;
            }
        }
        return false;
    }
}