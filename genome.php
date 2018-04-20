<?php
$target = 'to be or not to be that is the question';
$length = strlen($target);

$organisms = [];

for ($i = 0; $i < 1000; $i++) {
    $organisms[] = Organism::fromRandom($length);
}


for ($i = 0; $i < 1000; $i++) {
    $organisms = getNextGeneration($organisms, $target);

    foreach ($organisms as $organism) {
        if ($organism->getFitness() >= 1) {
            var_dump($organism);
            var_dump($i);
            die();
        }
    }
}

/**
 * @param Organism[] $organisms
 * @param string     $target
 *
 * @return array
 */
function getNextGeneration(array $organisms, string $target)
{
    $totalFitness = 0;

    $fittest = null;
    $bestFitness = 0;
    foreach ($organisms as $organism) {

        $fitness = fitness($target, $organism);

        if ($fitness > $bestFitness) {
            $bestFitness = $fitness;
            $fittest = $organism;
        }
        $organism->setFitness($fitness);

        $totalFitness += $organism->getFitness();
    }
    echo 'fittest: ' . $fittest . PHP_EOL;


    $nextGen = [];

    for ($i = 0; $i < 1000; $i++) {

        $mum = getRandomParent($organisms, $totalFitness);
        $dad = getRandomParent($organisms, $totalFitness);

//        echo 'mum: ' . $mum . PHP_EOL;
//        echo 'dad: ' . $dad . PHP_EOL;

        $child = $mum->breedWith($dad);

//        echo 'Child: ' . $child . PHP_EOL;

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







function fitness(string $target, Organism $organism)
{
    return  1 - (levenshtein($target, $organism->getGenotype()) / strlen($target));
}



class Organism
{
    private $genotype;
    private $fitness;

    /**
     * @return mixed
     */
    public function getFitness()
    {
        return $this->fitness;
    }

    /**
     * @param mixed $fitness
     */
    public function setFitness($fitness)
    {
        $this->fitness = $fitness;
    }

    /**
     * @return mixed
     */
    public function getGenotype()
    {
        return $this->genotype;
    }

    /**
     * Organism constructor.
     *
     * @param $genotype
     */
    public function __construct($genotype)
    {
        $this->genotype = $genotype;
    }

    public static function fromRandom(int $length): Organism
    {
        $genotype = '';

        do {
            $chr = chr(rand(97, 123));
            if ($chr == '{') {
                $chr = ' ';
            }
            $genotype .= $chr;
        } while (--$length);

        return new self($genotype);
    }

    public static function mutate(array $genotype): array {

        return $genotype;
    }

    public function breedWith(Organism $partner): Organism {

        $genotype = array_map(
            function($chr1, $chr2) {
                if (!mt_rand(0,99)) {
                    $chr = chr(rand(97, 123));
                    if ($chr == '{') {
                        $chr = ' ';
                    }

                    return $chr;
                }
                return mt_rand(0,1) ? $chr1 : $chr2;
            },
            str_split($this->genotype),
            str_split($partner->genotype)
        );

        $genotype = self::mutate($genotype);

        return new Organism(implode('', $genotype));

    }



    function __toString()
    {
        return $this->genotype . ' ' . $this->getFitness();
    }

}
