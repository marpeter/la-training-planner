<?php
namespace TnFAT\Planner;

class Utils {

    const ALLOWED_TAGS = '<b><em><i><strong>';

    static function getPostedString( string $fieldName ): string {
        if( isset($_POST[$fieldName]) && is_string($_POST[$fieldName]) ) {
            return trim($_POST[$fieldName]);
        } else {
            return '';
        }
    }
    static function getQueryString( string $fieldName ): string {
        if( isset($_GET[$fieldName]) && is_string($_GET[$fieldName]) ) {
            return trim($_GET[$fieldName]);
        } else {
            return '';
        }
    }

}