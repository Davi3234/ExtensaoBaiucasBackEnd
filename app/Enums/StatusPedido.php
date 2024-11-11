<?php
namespace App\Enum;

enum StatusPedido: string {
  case EM_PREPARO = 'EP';
  case CONCLUIDO  = 'CO';
  case EM_ENTREGA = 'EE';
  case CANCELADO  = 'CA';
}