<?php
namespace TnFAT\Planner;

abstract class EntityFormatterFactory {

    public static function getReader(string $entity, string $format="json"): EntityFormatter {  
        if( $format === '' || $format === 'json' ) {
            $formatterClass = "\\TnFAT\\Planner\\$entity\\ToJsonFormatter";
        } else if( $format === 'csv' ) {
            $formatterClass = "\\TnFAT\\Planner\\$entity\\ToCsvFormatter";
        } else {
            return [
                'success' => false,
                'message' => 'You must specify a valid format instead of: ' . htmlspecialchars($format),
            ];
        }
        return new $formatterClass();
    }
  }