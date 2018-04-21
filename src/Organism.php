<?php

class Organism
{
    private const NO_OF_CITIES = 10;
    private const GENOTYPE_LENGTH = 10;

    /** @var array */
    private $genotype;

    /** @var float */
    private $fitness;

    public function getFitness() : float
    {
        return $this->fitness;
    }

    public function setFitness(float $fitness)
    {
        $this->fitness = $fitness;
    }

    public function getGenotype() : array
    {
        return $this->genotype;
    }

    public function __construct(array $genotype)
    {
        $this->genotype = $genotype;
    }

    public static function fromRandom(): Organism
    {
        $genotype = [];

        for ($swapPairNo = 0; $swapPairNo < static::GENOTYPE_LENGTH; ++$swapPairNo) {
            $firstHalfOfSwapPair = rand(0,static::NO_OF_CITIES - 1);
            $secondHalfOfSwapPair = rand(0,static::NO_OF_CITIES - 1);
            $genotype[] = [$firstHalfOfSwapPair, $secondHalfOfSwapPair];
        }

        return new self($genotype);
    }

    public static function mutate(array $genotype): array
    {
        return $genotype;
    }

    public function breedWith(Organism $partner): Organism
    {
        $genotype = array_map(
            function($geneFromParent1, $geneFromParent2) {
                return mt_rand(0,1) ? $geneFromParent1 : $geneFromParent2;
            },
            $this->genotype,
            $partner->genotype
        );

        $genotype = self::mutate($genotype);

        return new Organism($genotype);

    }

    public function __toString()
    {
        return (string) var_export($this->genotype, true) . ' ' . $this->getFitness();
    }

    public function decodeToPhenotype(array $cityList) : array
    {
        foreach ($this->genotype as $gene) {
            $tmp = $cityList[$gene[0]];
            $cityList[$gene[0]] = $cityList[$gene[1]];
            $cityList[$gene[1]] = $tmp;
        }

        return $cityList;
    }
}
