<?php

spl_autoload_register(function ($class) {

    // error_log("Autoloading class: $class");
    // project-specific namespace prefix
    // TnFAT = Track and Field Athletic Training
    $prefix = 'TnFAT\\Planner\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/../docs/lib/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // error_log("Looking for file: $file");
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});