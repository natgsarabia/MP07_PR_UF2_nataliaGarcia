<?php
    require 'claseBase.php';

    Class Usuari extends Base{
        public $email;
        public $contrasenya;
        public $rol;

        //Métode contructor per incialitzar l'empleat
        public function __construct( string $nom, string $email='',string $contrasenya='',string $rol='')
        {
            //atributs heretats del pare
            parent::__construct( $nom);
            $this->email=$email;
            $this->contrasenya=$contrasenya;
            $this->rol=$rol;
        }
    }


?>