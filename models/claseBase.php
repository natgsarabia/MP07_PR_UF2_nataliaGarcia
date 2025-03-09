<?php
        Class Base{
            public $nom;
    
            //Métode contructor per incialitzar l'empleat
            public function __construct( string $nom='')
            {
                $this->nom=$nom;
            }
        }
?>