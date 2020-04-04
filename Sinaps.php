<?php
require_once 'Neuron.php';

class Sinaps{
    
    function __construct($name, $neuronIn, $neuronOut, $value = 0.0){
        $this ->name = $name;
        $this ->neuronIn = $neuronIn;
        $this ->neuronOut = $neuronOut;
        $this ->value = $value;
        $this ->grad = 0.0;
        $this ->deltaValueLast = 0.0;
    }
    
    function set_value($value) {$this ->value = $value;}
    function set_deltaValueLast($value) {$this ->deltaValueLast = $value;}	
    function name(){return $this ->name;}
    function neuronIn() {return $this ->neuronIn;}
    function neuronOut() {return $this ->neuronOut;}
    function value(){return $this ->value;}
    function deltaValueLast(){return $this ->deltaValueLast;}
}
?>
