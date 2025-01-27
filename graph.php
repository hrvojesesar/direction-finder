<?php
// Graf definiran kao lista susjedstva
$graph = [
    'A' => ['F' => 3, 'H' => 2.5],
    'F' => ['D' => 1, 'H' => 4],
    'H' => ['J' => 1, 'A' => 2.5, 'D' => 3],
    'J' => ['H' => 1, 'K' => 3, 'L' => 4],
    'D' => ['H' => 3, 'C' => 2, 'K' => 2],
    'K' => ['D' => 2, 'J' => 3, 'L' => 1],
    'L' => ['J' => 4, 'K' => 1, 'G' => 2],
    'G' => ['E' => 2, 'D' => 5],
    'E' => ['G' => 2, 'B' => 2, 'C' => 3],
    'B' => [],
    'C' => ['D' => 2, 'E' => 3, 'B' => 2],
];

// Funkcija za izračun udaljenosti određene rute
function calculatePathDistance($graph, $path)
{
    $distance = 0;
    for ($i = 0; $i < count($path) - 1; $i++) {
        $distance += $graph[$path[$i]][$path[$i + 1]];
    }
    return $distance;
}

// Dijkstraov algoritam
function dijkstra($graph, $start, $end)
{
    $distances = [];
    $previous = [];
    $queue = [];

    foreach ($graph as $node => $neighbors) {
        $distances[$node] = PHP_INT_MAX;
        $previous[$node] = null;
        $queue[$node] = PHP_INT_MAX;
    }
    $distances[$start] = 0;
    $queue[$start] = 0;

    while (!empty($queue)) {
        // Dohvat čvora s najmanjom udaljenosti
        $current = array_keys($queue, min($queue))[0];
        unset($queue[$current]);

        // Ako smo stigli na cilj, završavamo
        if ($current === $end) {
            break;
        }

        // Ažuriramo udaljenosti susjeda
        foreach ($graph[$current] as $neighbor => $distance) {
            $alt = $distances[$current] + $distance;
            if ($alt < $distances[$neighbor]) {
                $distances[$neighbor] = $alt;
                $previous[$neighbor] = $current;
                $queue[$neighbor] = $alt;
            }
        }
    }

    // Rekonstrukcija najkraće rute
    $path = [];
    for ($node = $end; $node !== null; $node = $previous[$node]) {
        array_unshift($path, $node);
    }

    return $distances[$end] === PHP_INT_MAX ? null : $path;
}

// DFS za pronalaženje svih ruta
function dfsFindAllPaths($graph, $current, $end, $path = [])
{
    $path[] = $current;

    if ($current === $end) {
        return [$path];
    }

    $paths = [];
    foreach ($graph[$current] as $neighbor => $distance) {
        if (!in_array($neighbor, $path)) {
            $newPaths = dfsFindAllPaths($graph, $neighbor, $end, $path);
            $paths = array_merge($paths, $newPaths);
        }
    }

    return $paths;
}


// Unos korisnika
echo "Unesite početni čvor: ";
$start = trim(fgets(STDIN));

echo "Unesite krajnji čvor: ";
$end = trim(fgets(STDIN));

// Provjera je li unos valjan
if (!isset($graph[$start]) || !isset($graph[$end])) {
    echo "Pogrešan unos čvorova. Provjerite unos i pokušajte ponovno.\n";
    exit(1);
}

// Pronalazak svih ruta i najkraće rute koristeći DFS
$allPathsDFS = dfsFindAllPaths($graph, $start, $end);
$shortestDistanceDFS = PHP_INT_MAX;


// Pronalazak najkraće rute koristeći Dijkstraov algoritam
$shortestPathDijkstra = dijkstra($graph, $start, $end);

// Ispis rezultata DFS
echo "\nDFS - Sve moguće rute od $start do $end:\n";
foreach ($allPathsDFS as $path) {
    echo implode(" -> ", $path) . "\n";
}


// Ispis rezultata Dijkstra
if ($shortestPathDijkstra) {
    $shortestDistanceDijkstra = calculatePathDistance($graph, $shortestPathDijkstra);
    echo "\nNajkraća ruta: " . implode(" -> ", $shortestPathDijkstra) . " (Udaljenost: $shortestDistanceDijkstra)\n";
} else {
    echo "\nNije pronađena ruta od $start do $end.\n";
}
