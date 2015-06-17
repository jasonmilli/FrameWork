<?php
spl_autoload_register('autoLoader');
function autoLoader($class) {
    chdir(__DIR__);
    $file = '../'.strtolower(str_replace('\\', '/', $class)).'.php';
    $parts = explode('\\', $class);
    if (file_exists($file)) {
        include $file;
    } elseif (count($parts) > 1 && $parts[0] == 'Work' && $parts[1] == 'Models') {
        $namespace = '';
        for ($i = 0; $i < count($parts) - 1; $i++) {
            $namespace .= $parts[$i];
            if (isset($parts[$i + 2])) $namespace .= '\\';
        }
        $name = $parts[count($parts) - 1];
        eval("namespace $namespace; class $name extends \Frame\Model {}");
    } else {
        print_r($parts);
        throw new Exception("File not found: $file");
    }
}
$model = new Frame\Model;
