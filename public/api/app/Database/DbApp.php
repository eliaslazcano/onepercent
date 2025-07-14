<?php

namespace App\Database;

use Eliaslazcano\Helpers\DatabaseController;
use Eliaslazcano\Helpers\HttpHelper;

class DbApp extends DatabaseController
{
  protected $host = 'aws.eliaslazcano.dev.br';
  protected $base_de_dados = 'onepercent';
  protected $usuario = 'root';
  protected $senha = '501zinh0';
  protected $timezone = '-04:00';

  protected function aoFalhar(string $mensagem, ?string $dadosLog = null): void
  {
    HttpHelper::erroJson(500, $mensagem);
  }
}