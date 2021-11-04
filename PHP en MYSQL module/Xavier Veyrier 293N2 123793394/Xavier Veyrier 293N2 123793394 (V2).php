<?php

    class Cirkel
    {
        public $naam, $diameter;

        function __construct($voorwerp, $diam)
        {
            $this->diameter = $diam;
            $this->naam = $voorwerp;
        }

        function oppervlakteCirkel()
        {
            $oppervlakte_cirkel = pi() * pow(($this->diameter / 2), 2);
            return $oppervlakte_cirkel; 
        }

        function omtrekCirkel()
        {
            $omtrek_cirkel = $this->diameter * pi();
            return $omtrek_cirkel; 
        }
    }


    $deksel = new Cirkel("deksel", 10);   
    $frisbee = new Cirkel("frisbee", 35);
    $dienblad = new Cirkel("dienblad", 75);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Cirkels</title>
    </head>
    <body>
        <ul>
            <li>De <?php echo $deksel->naam; ?> heeft een diameter van <?php echo $deksel->diameter; ?>cm, een omtrek van <?php echo $deksel->omtrekCirkel(); ?>cm en een oppervlak van <?php echo $deksel->oppervlakteCirkel() ?> vierkante centimeter.</li>
            <li>Mijn <?php echo $frisbee->naam; ?>, die makkelijk te hanteren is door zijn omtrek van <?php echo $frisbee->omtrekCirkel(); ?>cm, vliegt uitstekend door zijn oppervlak van <?php echo $frisbee->oppervlakteCirkel(); ?> vierkante centimeter.</li>
            <li>Dit <?php echo $dienblad->naam ?> is door zijn oppervlak van <?php echo $dienblad->oppervlakteCirkel(); ?>cm^2 handig om veel spullen te dragen, maar door zijn omtrek van <?php echo $dienblad->omtrekCirkel(); ?>cm niet handig om te gebruiken in een krappe ruimte!</li>
        </ul>
    </body>
</html>
