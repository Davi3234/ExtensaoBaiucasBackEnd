<?php
    namespace App\Model;
    class Field{
        private $ref;
        private $width;
        private $options = [];
        private $readonly = false;
        private $visible = true;
        private $required = true;
        private $resetable = false;
        private $tooltip = '';
        private $mask = '';
        private $placeholder = '';
        private $inputType;
        private $style = '';
        private $label = '';
    }