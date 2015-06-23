<?php
chdir(__DIR__);
require '../../frame/index.php';
$climbers = new Work\Models\Climber\Climbers;
/*print_r($climbers->create(array('first_name' => 'Bob', 'last_name' => 'Bobson')));
print_r($climbers->read(array('first_name' => 'Bob')));
print_r($climbers->update(2, array('last_name' => 'tttt')));
print_r($climbers->read(array('first_name' => 'Bob')));
print_r($climbers->delete(3));
print_r($climbers->read(array('first_name' => 'Bob')));*/
$routes = new Work\Models\Climber\Routes;
print_r($routes->create(array('climber_id' => 5, 'grade' => 'XXX', 'route' => 'Crack love', 'crag' => 'quarry')));
print_r($climbers->join($routes));
