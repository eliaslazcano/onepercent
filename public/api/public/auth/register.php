<?php
/**
 * POST => Cria um JWT para navegar pela area restrita do app.
 */

use App\Auth;
use App\Config;
use App\Database\DbApp;
use Eliaslazcano\Helpers\HttpHelper;
use Eliaslazcano\Helpers\Utils;

HttpHelper::validarPost();

HttpHelper::validarJson();
$params = HttpHelper::validarParametros(['email','name','password']);

$db = new DbApp();

$sql = "SELECT id FROM users WHERE email = :email";
$usuarioInfo = $db->queryPrimeiraLinha($sql, [':email' => $params['email']]);
if ($usuarioInfo) HttpHelper::erroJson(400, 'Já existe uma conta com este email.');

$sql = "INSERT INTO users (email, name, password) VALUES (:email, :name, :password)";;
$usuarioId = (int) $db->insert($sql, [
  ':email' => $params['email'],
  ':name' => $params['name'],
  ':password' => md5($params['password'])
]);

$jwt = Utils::throwErroJson(fn() => Auth::criarSessao($usuarioId, $db->getConexao(), Config::SESSION_TTL, $params['name']));

HttpHelper::emitirJson(['token' => $jwt]);