<?php
    require 'claseBase.php';

    Class Anunci extends Base{
        public $id;
        public $descripcio;
        public $preu;
        public $categoria;
        public $imgProducte;
        public $usuari_id;
        public $data_registre;

        //Métode contructor per incialitzar l'empleat
        public function __construct( int $id,string $nom,string $descripcio='',int $preu=0,string $categoria='', string $imgProducte='', int $usuari_id)
        {
            
            $this->id=$id;
             //atributs heretats del pare
            parent::__construct( $nom);
            $this->descripcio=$descripcio;
            $this->preu=$preu;
            $this->categoria=$categoria;
            $this->imgProducte=$imgProducte;
            $this->usuari_id=$usuari_id;
            $this->data_registre=date('d-m-y');
        }
    }


?>