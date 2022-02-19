<?php
header("Location: results.php?search=home");

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

echo $twig->render(
    'index.html.twig',
    [
        "title" => "Hello!",
    ],
);

?>