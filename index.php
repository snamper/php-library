<?php
    require 'autoloader.php';

    $autoloader=new autoloader(array('org'));
    $autoloader->addSuffix('.class.php');
    $autoloader->register();






