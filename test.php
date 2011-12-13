<?php

require 'lib/JsonValidator.php';

/*
$o = new stdClass();
$o->name = 'Sample Book';
$o->author = 'John Doe';
$o->isbn = '1234567';
$o->publisher = 'ACME, Inc.';
$o->price = 49.99;
 */

$car = new stdClass();
$car->manufacturer = 'Chevrolet';
$car->model = 'Camaro';
$car->year = 2012;
$car->color = 'Red';
$car->engine = new stdClass();
$car->engine->cylinders = 8;
$car->engine->displacement = 6000;
$car->engine->horsepower = 350;
$car->engine->torque = 300;
$car->alloys = true;

$car->features = array(
    'foo', 'baz', 'bah'
);

$car->id = "ASDF";

$v = new JsonValidator('example/car.json');
$v->validate($car);