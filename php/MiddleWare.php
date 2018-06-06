<?php

require "../vendor/autoload.php";

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$app = new Slim\App();

$app->map(["GET"], "/thisFunction", function(RequestInterface $request, ResponseInterface $response) use ($app) {

    $cache = new Memcache;

    /* sanitise and read parameter */
    $params     = explode("&", $request->getUri()->getQuery());
    $fileToRead = filter_var($params["fileToRead"]);

    /* respond with cached data; saves hw and speeds up ux */
    if ($cachedContent = $cache->get(sha1($fileToRead))) {
        $response->withBody($cachedContent);
        $response->withStatus(200);
        $app->respond($response);
    }

    /* do logic */
    $fileContent = getData($fileToRead);

    /* error out on failure */
    if ($fileContent === FALSE) {
        $response->withBody(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "failure.html"));
        $response->withStatusCode(404);
        $app->respond($response);
    }

    /* continue on success */
    $cache->add(sha1($fileToRead), $fileContent);
    $response->withBody($fileContent);
    $response->withStatusCode(200);
    $app->respond($response);
});

/**
 * represents fetching data in a re-usable or removable code block;
 * in real life this would be a data model in its own class
 * @param string $file : key to data source
 * @return string | false on failure
 */
function getData($file) {

    $filename = implode(DIRECTORY_SEPARATOR, ["path", "to", "files", $file]);
    /* if we fail here, spare hw resources */
    if (!file_exists($filename)) {
        return FALSE;
    }

    /* return data if we can read it */
    if ($content = file_get_contents($filename)) {
        return $content;
    }

    return FALSE;
}
