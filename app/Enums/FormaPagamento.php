<?php
namespace App\Enum;

enum FormaPagamento: string {
  case CARTAO = 'C';
  case DINHEIRO = 'D';
  case PIX = 'P';
}