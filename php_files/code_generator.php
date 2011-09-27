<?php    
    $toString = "";
    $toArray  = "array(";
    for($i = 1; $i < count($argv); $i++){
        if($i == count($argv) - 1){
            $toString .= "\t\"Field " . ucfirst($argv[$i]) . ":\". \$this->".$argv[$i]."\n\t\t.\"\\n\"";
            $toArray .= "\n\t\t\"". $argv[$i] ."\"=>\$this->".$argv[$i].")";
        }
        else{
            $toString .= "\t\"Field " . ucfirst($argv[$i]) . ":\". \$this->".$argv[$i]."\n\t\t.\"\\n\".";        
            $toArray .= "\n\t\t\"". $argv[$i] ."\"=>\$this->".$argv[$i].",";
        }
        
        $str = "\tpublic function set" .ucfirst($argv[$i]) . "(\$p".ucfirst($argv[$i]) . "){\n" .
                "\t\t\$this->".$argv[$i]."=\$p".ucfirst($argv[$i]) . "; \n\t\treturn \$this;\n\t}\n\n";
        $str .= "\tpublic function get" .ucfirst($argv[$i]) . "(){\n" .
                "\t\treturn \$this->".$argv[$i].";\n\t}\n\n\n";
        
        echo($str);
    }
    echo("\tpublic function toString(){\n\t\treturn " . $toString . ";\n\t}\n");
    echo("\tpublic function toArray(){\n\t\treturn " . $toArray . ";\n\t}\n");
?>