<?php
chdir(__DIR__);
require '../../frame/index.php';
$climbers = new Work\Models\Climber\Climbers;
print_r($climbers->create(array('first_name' => 'Bob', 'last_name' => 'Bobson')));
print_r($climbers->read(array('first_name' => 'Bob')));
print_r($climbers->update(1, array('second_name' => 'tttt')));
print_r($climbers->read(array('first_name' => 'Bob')));
print_r($climbers->delete(1));
print_r($climbers->read(array('first_name' => 'Bob')));
