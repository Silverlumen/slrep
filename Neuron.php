<?php
class Neuron{
    public
    $delta = 0.0;
    
    function __construct($name = "", $row = 0, $value = 0.0){
        $this ->name = $name;
        $this ->row = $row;
        $this ->value = $value;
    }

    function set_delta($delta){ $this ->delta = $delta;}
    function set_value($value) {$this ->value = $value;}
    function delta() {return $this ->delta;}
    function value() {return $this ->value;}
    function name() {return $this ->name;}
    function row() {return $this ->row;}
}
?>
