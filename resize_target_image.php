<?php
require 'ImageResizer.php';
$ir=new ImageResizer($argv[1],array('red'=>255,'green'=>255,'blue'=>255));
$ir->resizeAndSave($argv[2],$argv[3],$argv[4]);
