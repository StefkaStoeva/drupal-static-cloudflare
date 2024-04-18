<?php

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Adjust the autoload path according to your Drupal installation
$autoloader = require_once 'web/autoload.php';

$request = Request::createFromGlobals();
$kernel = new DrupalKernel('prod', $autoloader, true);
$kernel->boot();
$kernel->preHandle($request);
$request->attributes->set('_site_path', 'web');

// Replace with the actual method to fetch or define your URLs
$urls = ['/node/1', '/about-us', '/contact'];

foreach ($urls as $url) {
    // Simulate a request to each URL
    $currentRequest = Request::create($url, 'GET', [], [], [], $_SERVER);
    $response = $kernel->handle($currentRequest);

    // Extract the HTML content
    $htmlContent = $response->getContent();

    // Define the output file path
    // Ensure the directory structure exists or is created
    $outputDir = __DIR__ . '/static_html';
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0755, true);
    }
    $outputFilePath = $outputDir . str_replace('/', '_', $url) . '.html';

    // Save the HTML content to a file
    file_put_contents($outputFilePath, $htmlContent);
}

