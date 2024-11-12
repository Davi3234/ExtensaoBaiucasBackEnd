<?php

namespace App\Enums;

enum FormaPagamento: string {
  case CARTAO = 'C';
  case DINHEIRO = 'D';
  case PIX = 'P';
}
