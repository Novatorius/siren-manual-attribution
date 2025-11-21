<?php
// PSR/4 Autoloader for Novatorius\Siren\ManualAttribution

spl_autoload_register(function ($class) {
    // Namespace prefix
    $prefix = 'Novatorius\\Siren\\ManualAttribution';
    $baseDir = plugin_dir_path(__FILE__) . 'lib/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relativeClass = substr($class, $len);

    // Replace namespace separators with directory separators, append .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
