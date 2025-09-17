<?php
$scriptName = isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : "";
$scriptName = str_replace("\\", "/", $scriptName);
$baseDir = rtrim(dirname($scriptName), "/");

$requestUri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "/";
$requestPath = parse_url($requestUri, PHP_URL_PATH);
if (!is_string($requestPath) || $requestPath === "") {
    $requestPath = "/";
}
$query = parse_url($requestUri, PHP_URL_QUERY);

$path = $requestPath;
if ($baseDir !== "" && strpos($path, $baseDir) === 0) {
    $path = substr($path, strlen($baseDir));
}
$path = "/" . ltrim($path, "/");

$publicBase = $baseDir === "" ? "/public" : $baseDir . "/public";
$target = rtrim($publicBase, "/");

if ($path === "/" || $path === "/index.php") {
    $target .= "/";
} else {
    $target .= $path;
}

if (is_string($query) && $query !== "") {
    $target .= "?" . $query;
}

header("Location: " . $target, true, 302);
exit;

