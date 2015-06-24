<?php
spl_autoload_register('autoLoader');
function autoLoader($class) {
    chdir(__DIR__);
    $file = '../'.strtolower(str_replace('\\', '/', $class)).'.php';
    $parts = explode('\\', $class);
    if (file_exists($file)) {
        include $file;
    } elseif (count($parts) == 4 && $parts[0] == 'Work' && $parts[1] == 'Models') {
        $php = <<<PHP
namespace {$parts[0]}\\{$parts[1]}\\{$parts[2]};
class {$parts[3]} extends \\Frame\\Model {
    public function __construct() {
        parent::__construct(strtolower('{$parts[2]}'), strtolower('{$parts[3]}'));
    }
}
PHP;
        eval($php);
    } else {
        print_r($parts);
        throw new Exception("File not found: $file");
    }
}
chdir(__DIR__);
require '../work/main.php';
