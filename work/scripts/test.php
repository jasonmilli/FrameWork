<?php
chdir(__DIR__);
require '../../frame/index.php';
$climbers = new Work\Models\Climber\Climbers;
print_r($climbers->create(array('first_name' => 'Bob', 'last_name' => 'Bobson')));
