<?php
$dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$target = $dir === '' ? '/public/' : $dir . '/public/';
header('Location: ' . $target, true, 302);
exit;