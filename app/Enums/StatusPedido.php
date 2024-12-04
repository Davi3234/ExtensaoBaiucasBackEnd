<?php

namespace App\Enums;

enum StatusPedido: string
{
  case EM_PREPARO = 'EP';
  case FINALIZADO = 'FI';
  case EM_ENTREGA = 'EE';
  case CANCELADO  = 'CA';
}
