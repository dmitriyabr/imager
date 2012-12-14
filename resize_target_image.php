<?php
require 'ImageResizer.php';
$ir=new ImageResizer($argv[1]);
$ir->resizeAndSave($argv[2],$argv[3],$argv[4]);
