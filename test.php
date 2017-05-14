<?php
    $controllerName = 'test-ich-jetzt-na-dann';
    echo ucFirst(preg_replace_callback('/(\-[a-z]{1})/', "upperCase", $controllerName));

    function upperCase(array $piece) {
        return ucfirst(str_replace('-', '', $piece[1]));
    }

    function generatePathDepth($neededDepth, $currentDepth, $currentPath) {

    }

    $db = mysqli_connect('172.18.0.2', 'root', 'dwj8YvVHdLVP', 'site_test');

    $maxSitesCount = 500000;
    $sites = [];

    for ($site = 1; $site < $maxSitesCount; ++$site) {
        $currentDepth = rand(0, 15);
        $path = '/';

        for ($depth = 0; $depth < $currentDepth; ++$depth) {
            $path .= $depth . '/';
        }

        $sites[$site] = [
            'site_id' => $site,
            'path' => $path,
            'depth' => $currentDepth
        ];
    }

    mysqli_query($db, 'TRUNCATE sites');

    $insertString = 'INSERT INTO sites (`site_id`, `path`, `depth`) VALUES ';
    $queryString = '';

    $currentInsertCount = 0;
    foreach ($sites as $site) {
        ++$currentInsertCount;
        $queryString .= '("' . $site['site_id'] . '", "' . $site['path'] . '", "' . $site['depth'] . '"), ';

        if (100000 <= $currentInsertCount) {
            $queryString = substr($queryString, 0,  -2);
            mysqli_query($db, $insertString . $queryString);
            $queryString = '';
            $currentInsertCount = 0;
        }
    }

    if (0 < strlen($queryString)) {
        $queryString = substr($queryString, 0,  -2);
        mysqli_query($db, $insertString . $queryString);
    }

    echo PHP_EOL . "Benchmark with Path count only:";
    $start = microtime(true);
    $query = "SELECT SQL_NO_CACHE site_id, (LENGTH(path) - LENGTH(REPLACE(path, '/', ''))) AS path_depth FROM sites";
    mysqli_query($db, 'RESET QUERY CACHE');
    $result = mysqli_query($db, $queryString);
    $end = microtime(true);

    echo "Time : " . ($end - $start);

    echo PHP_EOL . PHP_EOL . "Benchmark Depth:";
    $start = microtime(true);
    $query = "SELECT SQL_NO_CACHE site_id, depth FROM sites";
    mysqli_query($db, 'RESET QUERY CACHE');
    $result = mysqli_query($db, $queryString);
    $end = microtime(true);

    echo "Time : " . ($end - $start);

    echo PHP_EOL . PHP_EOL . "Benchmark Path and Depth:";
    $start = microtime(true);
    $query = "SELECT SQL_NO_CACHE site_id, (LENGTH(path) - LENGTH(REPLACE(path, '/', ''))) AS path_depth, depth FROM sites";
    mysqli_query($db, 'RESET QUERY CACHE');
    $result = mysqli_query($db, $queryString);
    $end = microtime(true);

    echo "Time : " . ($end - $start);