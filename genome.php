<?php

require_once __DIR__ . '/vendor/autoload.php';

const CITY_LIST = [
    [ 0.00, 100.00 ],
    [ 58.75, 80.92 ],
    [ 95.09, 30.96 ],
    [ 95.14, -30.81 ],
    [ 58.88, -80.83 ],
    [ 0.16, -100.00 ],
    [ -58.62, -81.01 ],
    [ -95.04, -31.11 ],
    [ -95.18, 30.66 ],
    [ -59.01, 80.73 ]
];

const POPULATION_SIZE = 500;
const MAX_EVOLUTION_CYCLES = 1000;

$organisms = [];

$shortestPossibleRoute = routeDistance(CITY_LIST);

echo sprintf(
    'Shortest possible route = %.2f%s',
    $shortestPossibleRoute,
    PHP_EOL
);

for ($i = 0; $i < POPULATION_SIZE; $i++) {
    $organisms[] = Organism::fromRandom();
}


for ($i = 0; $i < MAX_EVOLUTION_CYCLES; $i++) {
    $organisms = getNextGeneration($organisms, $shortestPossibleRoute);
}

/**
 * @param Organism[] $organisms
 * @param string     $target
 *
 * @return array
 */
function getNextGeneration(array $organisms, float $shortestPossibleRoute)
{
    $longestRoute = 0.0;
    $shortestRoute = 999999999;
    $shortestCityList = null;

    foreach ($organisms as $organism) {

        $phenotype = $organism->decodeToPhenotype(CITY_LIST);
        $routeDistance = routeDistance($phenotype);

        if ($routeDistance > $longestRoute) {
            $longestRoute = $routeDistance;
        }

        if ($routeDistance < $shortestRoute) {
            $shortestRoute = $routeDistance;
            $shortestCityList = $phenotype;
        }

        $organism->setFitness($routeDistance);
    }

    echo sprintf('Longest route = %.2f', $longestRoute), PHP_EOL;
    echo sprintf('Shortest route = %.2f', $shortestRoute), PHP_EOL;

    if ($shortestRoute <= $shortestPossibleRoute) {
        die(
            sprintf(
                '*** Solved ***%sRoute: %s%s',
                PHP_EOL,
                json_encode($shortestCityList),
                PHP_EOL
            )
        );
    }

    $totalFitness = 0;
    $fittest = reset($organisms);

    foreach ($organisms as $organism) {

        $organism->setFitness($longestRoute - $organism->getFitness());

        if ($organism->getFitness() > $fittest->getFitness()) {
            $fittest = $organism;
        }

        $totalFitness += $organism->getFitness();
    }

    $nextGen = [];

    for ($i = 0; $i < count($organisms); $i++) {

        $mum = getRandomParent($organisms, $totalFitness);
        $dad = getRandomParent($organisms, $totalFitness);

        $child = $mum->breedWith($dad);

        $nextGen[] = $child;
    }

    return $nextGen;
}






function getRandomParent(array $organisms, $totalFitness): Organism {

    $rouletteWheelValue = $totalFitness * mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();

    foreach ($organisms as $organism) {

        $rouletteWheelValue -= $organism->getFitness();

        if ($rouletteWheelValue < 0) {
            return $organism;
        }
    }

    return $organism;
}

function routeDistance(array $cityList) : float
{
    $start = end($cityList);
    reset($cityList);
    $totalDistance = 0;

    foreach ($cityList as $nextCity) {

        $distanceBetweenCities = sqrt(
            pow($start[0] - $nextCity[0], 2) +
            pow($start[1] - $nextCity[1], 2)
        );

        $totalDistance += $distanceBetweenCities;

        $start = $nextCity;
    }

    return $totalDistance;
}
