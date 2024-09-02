<?php
    namespace App\Model;
    class Form{
        private $id;
        private $title;
        private $layout;
        private $class = '';
        private $expanded = false;

        function __construct($id, $title, $layout){
            $this->id = $id;
            $this->title = $title;
            $this->layout = $layout;
        }
        
        function Get($propName){
            return $this->$propName;
        }
        function Set($propName, $propValue){
            $this->$propName = $propValue;
        }

    }