<?php
require_once 'Neuron.php';
require_once 'Sinaps.php';


class NeuronNet{
    public
    function __construct(){
        $this ->sinapsArray = [];
        $this ->neuronArray = [];
        $this ->epsilon = 0.3;
        $this ->alfa = 0.5;

        $this ->outputNeuronCash = new Neuron('CASH', 0, -1);
        $this ->rowsCash = 0;
    }
    function set_rowsCash($value) {$this->rowsCash = $value;} 
    function set_sinapsArray($value) {$this->sinapsArray = $value;}
    function set_neuronArray($value) {$this->neuronArray = $value;}
    function set_outputNeuronCash($value) {$this->outputNeuronCash = $value;}
    function epsilon() {return $this->epsilon;} 
    function alfa() {return $this->alfa;}
    function sinapsArray() {return $this->sinapsArray;}
    function neuronArray() {return $this->neuronArray;}
    function outputNeuronCash() {return $this->outputNeuronCash;}

    function countRows(){
        
        $rowsCash = 0;
        $rows = 0;
        foreach ($this->neuronArray as $neuron){
            if ($rows < $neuron->row()) {
                $rows = $neuron->row();
            }
	    $rowsCash = $rows + 1;
        }
        return $rowsCash;
    }
    
    function getNeuronsFromRow($rowNumber){
        $neurons = [];
        foreach ($this ->neuronArray as $neuron){
            if($rowNumber == $neuron->row()){
                 array_push($neurons, $neuron);
            }
        }
        return $neurons;
    }

    function getInSinapses($neuron){    
        $sinapses = [];
        #echo "Neuron in getInSinapses is neuron.name}"
        foreach ($this ->sinapsArray as $sinaps){
            #echo "Out sinaps.neuronOut.name} in  neuron.name}"
            if ($sinaps->neuronOut()->name() == $neuron->name()){
                array_push($sinapses, $sinaps);
            }
        }
        return $sinapses;
    }

    function activationFunction($inValue){
        return 1.0/(1.0 + exp(0 - $inValue));
    }

    function countNeuronValue($inSinapses){
        $h1 = 0.0;
        foreach ($inSinapses as $sinaps){
             $h1 = $h1 + $sinaps->value() * $sinaps->neuronIn->value();
        }
        $hOutpit = $this->activationFunction($h1);
        return $hOutpit;
    }
    
    function runNeuron(){
        $rows = $this->countRows();
        #echo "RUN NEURON!\n";
        for ($i=1; $i<=$rows-1; $i++){
            #echo "Counting row: $i";
            $neurons = $this->getNeuronsFromRow($i);
            foreach ($neurons as $neuron){ 
                $inSinapses = $this->getInSinapses($neuron);
                #echo $neuron;
                #echo $inSinapses;
                $neuron->set_value($this->countNeuronValue($inSinapses));
                #echo "Neuron $neuron.name value: $neuron.value";
                #echo "$i <<>> $this ->$rows - 1}  value $value";
                if ($i == $rows - 1){
                    return $neuron->value();
	        }
            }
        }
    }
    
    function fSigmoid($out){
        return (1 - $out) * $out;
    }

    function getOutputNeuron(){
        if ($this->outputNeuronCash()->row() == 0){
            $rows = $this->countRows();
            $this->set_outputNeuronCash($this->getNeuronsFromRow($rows-1)[0]);
        }
        return $this->outputNeuronCash();
    }
    
    function sigmaOutput($outIdeal, $outActual){
        #echo "OutIdeal: $outIdeal}";
        #echo "OutActual: $outActual}";
        return ($outIdeal - $outActual) * $this->fSigmoid($outActual);
    }

    function mor($grad, $dw){
        return $this ->epsilon() * $grad + $this ->alfa() * $dw;
    }

    function grad($a, $b){
        return $a * $b;
    }

    function sigmaHidden($neuron){
        $outSinapses = $this->getOutSinapses($neuron);
        $fSigmoid = $this->fSigmoid($neuron->value());
        $sum = 0;
        foreach ($outSinapses as $sinaps){
            $sum = $sum + $sinaps->value() * $sinaps->neuronOut()->delta();
        }
        return $sum * $fSigmoid;
    }

    function teachInNeuronSinapses($neuron){
        $neuronSinapses = $this->getInSinapses($neuron);
        foreach ($neuronSinapses as $sinaps){
            $n = $sinaps->neuronIn();
            $n->set_delta($this->sigmaHidden($n));
            #echo "$n->value(), $sinaps->deltaValueLast()\n";
            $grad = $n->value() * $sinaps->neuronOut()->delta();
            $dw = $this->mor($grad, $sinaps->deltaValueLast());
            $sinaps->set_deltaValueLast($dw);
            $sinaps->set_value($sinaps->value() + $dw);
            #echo "Sinaps sinaps.name}, grad: grad}, dw: dw}, value: sinaps.value}"    
        }
    }

    function teachNeuronNetItirarion($outIdeal, $outActual){
        $outNeuron = $this->getOutputNeuron();
        $outNeuron->set_delta($this->sigmaOutput($outIdeal, $outActual));
        #echo "TEACH NEURON NET ITERATION\n";
        #echo "Out neuron (".$outNeuron->name()." delta: ".$outNeuron->delta()."\n";
        $this->teachInNeuronSinapses($outNeuron);
        for ($i=0; $i<= $outNeuron->row() - 1; $i++){
            #echo "Row: $i";
            $neurons = $this->getNeuronsFromRow(($outNeuron->row() - 1) - $i);
            foreach ($neurons as $neuron){
                #echo "Neuron sinaps study ".$neuron->name()."\n";
                $this->teachInNeuronSinapses($neuron);
            }
        }
     }

    function fTangh($out){
        return 1 - $out * $out;
    }

    function getOutSinapses($neuron){
        $sinapses = [];
        foreach ($this->sinapsArray as $sinaps){
            if ($sinaps->neuronIn()->name() == $neuron->name()){
                array_push($sinapses, $sinaps);
            }
        }
        return $sinapses;
    }
}

class ResultTableLine{
    function __construct($a = 0.0, $b = 0.0, $c = 0.0){
        $this ->a = $a;
        $this ->b = $b;
        $this ->c = $c;
        $this ->result = 0.0;
    }
    function set_result($value) {$this->result = $value;}
    function set_a($value) {$this->a = $value;}
    function set_b($value) {$this->b = $value;}
    function set_c($value) {$this->c = $value;}
    function a() {return $this->a;}
    function b() {return $this->b;}
    function c() {return $this->c;}
    function result() {return $this->result;}
}
      
function offset($xorResultTable, $delta){
    foreach ($xorResultTable as $r){
        #echo $r->c()." ".$r->result()." ".$delta."\n";
        if ($r->c() < $r->result() - $delta or $r->c() > $r->result() + $delta){
            return false;
        }
    }
    return true;
}

function main(){
   
    $xorResultTable = [new ResultTableLine(0, 0, 0),
                    new ResultTableLine(0, 1, 1),
                    new ResultTableLine(1, 0, 1),
                    new ResultTableLine(1, 1, 0)];

  
    $acc = 0.01;   

    $i1 = new Neuron("I1", 0);
    $i2 = new Neuron("I2", 0);
    $h1 = new Neuron("H1", 1);
    $h2 = new Neuron("H2", 1);
    $o1 = new Neuron("O1", 2);

    
    $w1 = new Sinaps("w1",$i1, $h1, 0.45);
    $w2 = new Sinaps("w2",$i1, $h2, 0.78);
    $w3 = new Sinaps("w3",$i2, $h1, -0.12);
    $w4 = new Sinaps("w4",$i2, $h2, 0.13);
    $w5 = new Sinaps("w5",$h1, $o1, 1.5);
    $w6 = new Sinaps("w6",$h2, $o1, -2.3);

    $i1->set_value(1.0);
    $i2->set_value(0.0);
    $nn = new NeuronNet();
    
    $nn->set_sinapsArray([$w1, $w2, $w3, $w4, $w5, $w6]);
    $nn->set_neuronArray([$i1, $i2, $h1, $h2, $o1]);

    echo "TEST FROM EXAMPLE\n";
    

    
    $ans = $nn->runNeuron();
    echo "First ans: $ans \n";
    $nn->teachNeuronNetItirarion(1, $ans);
    $ans = $nn->runNeuron();
    echo "Second ans: $ans \n";

    echo "RUNING STUDY \n";
    $i = 0;
    
    $previous = round(microtime(1)*1000);

    $startTime = round(microtime(1)*1000);
    while (true){ 
        foreach($xorResultTable as $r){
            $i1->set_value($r->a());
            $i2->set_value($r->b());
            $ans = $nn->runNeuron();
            $nn->teachNeuronNetItirarion($r->c(), $ans);
            $r->set_result($ans);
        }
        if ($i%100000 == 0){
            $current = round(microtime(1)*1000);
            $delta = $current - $previous;
            $previous = $current;
            #echo time();
            echo "Iteration $i, W: ".$xorResultTable[0]->c().", H: ".$xorResultTable[0]->result().", W: ".$xorResultTable[1]->c().", H: ".$xorResultTable[1]->result().", W: ".$xorResultTable[2]->c().", H: ".$xorResultTable[2]->result().", W: ".$xorResultTable[3]->c().", H: ".$xorResultTable[3]->result().", deltaTime: $delta \n";       
            if (offset($xorResultTable, $acc)){      
      	         break;
            }
        }
        $i++;
    $stopTime = round(microtime(1)*1000);
    $deltaMs = $stopTime - $startTime;
    $deltaS = $deltaMs/1000;
    }
    echo "Study time [s]: $deltaS, \nStudy time [ms]: $deltaMs \n";
}

main();

?>
