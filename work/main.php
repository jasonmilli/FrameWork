<?php
$climber = new Work\Models\Climber\Climbers;
print_r($climber->read());
echo Frame\View::html(Frame\View::head(), Frame\View::body(Frame\View::layoutColumn(array('Header', Frame\View::layoutRow(array('Menu', Frame\View::table($climber->read()))), 'Footer'))));
