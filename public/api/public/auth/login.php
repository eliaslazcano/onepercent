<?php

use App\Auth;
use App\Config;
use App\Database\DbApp;
use Eliaslazcano\Helpers\HttpHelper;
use Eliaslazcano\Helpers\Utils;

HttpHelper::validarPost();

HttpHelper::validarJson();
$params = HttpHelper::validarParametros(['email','password']);

$db = new DbApp();

$sql = "SELECT id, name, password FROM users WHERE email = :email";
$usuarioInfo = $db->queryPrimeiraLinha($sql, [':email' => $params['email']]);
if (!$usuarioInfo) HttpHelper::erroJson(400, 'Não existe uma conta com este email.');

if ($usuarioInfo['password'] !== md5($params['password'])) HttpHelper::erroJson(400, 'Senha incorreta');

$jwt = Utils::throwErroJson(fn() => Auth::criarSessao($usuarioInfo['id'], $db->getConexao(), Config::SESSION_TTL, $usuarioInfo['name']));

HttpHelper::emitirJson(['token' => $jwt]);