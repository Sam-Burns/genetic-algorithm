<?php

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