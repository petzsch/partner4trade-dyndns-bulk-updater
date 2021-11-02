<?php

require './config.php';

$ipv4 = $_GET['ipv4'];

file_put_contents('ipv4.txt', $ipv4);
