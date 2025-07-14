<?php

namespace App;

use App\Database\DbApp;
use Eliaslazcano\Helpers\HttpHelper;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class Auth
{
  /**
   * Remove o pedaço 'Bearer ' da string caso possua.
   * @param string $string
   * @return string
   */
  private static function removeBearer(string $string): string
  {
    if (!str_contains($string, 'Bearer ')) return $string;
    return trim(str_replace('Bearer ', '', $string));
  }

  /**
   * Obtem o token do header 'Authorization', sem a palavra Bearer (caso possua).
   * @return string|null
   */
  public static function getBearerToken(): ?string
  {
    $token = HttpHelper::obterCabecalho('Authorization');
    return $token ? self::removeBearer($token) : null;
  }

  /** @throws Exception */
  private static function jsonDecode(string $input, bool $associative = false)
  {
    $obj = json_decode($input, $associative, 512, JSON_BIGINT_AS_STRING);
    if ($errno = json_last_error()) {
      $messages = [
        JSON_ERROR_DEPTH => 'Profundidade máxima da pilha excedida',
        JSON_ERROR_STATE_MISMATCH => 'JSON inválido ou malformado',
        JSON_ERROR_CTRL_CHAR => 'Caractere de controle inesperado encontrado',
        JSON_ERROR_SYNTAX => 'Erro de sintaxe, JSON malformado',
        JSON_ERROR_UTF8 => 'Caracteres UTF-8 malformados' //PHP >= 5.3.3
      ];
      throw new Exception($messages[$errno] ?? 'Erro desconhecido de JSON: ' . $errno);
    }
    return $obj;
  }

  /**
   * Cria um token de autenticação para um usuário.
   * @param int $usuarioId ID do usuário.
   * @param PDO $conn Conexão com o banco de dados que guarda os dados das sessoes.
   * @param int $tempoExpiracao Duração da sessão em segundos de ausencia.
   * @param string|null $nome Nome do usuário para incluir no payload, se informado evita uma consulta SQL.
   * @return string Token da sessão (JWT), null em caso de erro.
   * @throws Exception
   */
  public static function criarSessao(int $usuarioId, PDO $conn, int $tempoExpiracao = 3600, ?string $nome = null): string
  {
    $db = new DbApp($conn);
    $sql = "INSERT INTO user_sessions (user, ip, ttl, expires_at) VALUES (:user, :ip, :ttl, CURRENT_TIMESTAMP + INTERVAL :ttl SECOND)";
    $sessaoId = (int) $db->insert($sql, [
      ':user' => $usuarioId,
      ':ip' => HttpHelper::getIp(),
      ':ttl' => $tempoExpiracao
    ]);

    if (!$nome) {
      $sql = "SELECT name FROM users WHERE id = :id";
      $usuarioInfo = $db->queryPrimeiraLinha($sql, [':id' => $usuarioId]);
      if (!$usuarioInfo) throw new Exception('A conta de usuá não foi encontrada com o ID informado.');
      $nome = $usuarioInfo['name'];
    }

    $key = md5(uniqid(mt_rand().mt_rand(), true));
    $payload = [
      'user' => $usuarioId,
      'name' => $nome,
      'session' => $sessaoId
    ];
    $jwt = JWT::encode($payload, $key, 'HS256');

    $sql = "UPDATE user_sessions SET secret = :secret WHERE id = :id";
    $db->update($sql, [':secret' => $key, ':id' => $sessaoId]);

    return $jwt;
  }

  /**
   * Confere se o cliente está autenticado, ele precisa ter enviado um token no header 'Authorization' ou pode especificar manualmente.
   * @param PDO|null $conn Conexão com o banco de dados que guarda os dados das sessoes.
   * @param string|null $token Especifica manualmente um token JWT, ignorando o que houver no header 'Authorization'.
   * @param bool $exception Emite exception se a autenticação reprovar ou houver falha.
   * @return array - Payload da sessão em array associativo.
   * @throws Exception
   */
  public static function autenticarSessao(?PDO $conn = null, ?string $token = null, bool $exception = false): array
  {
    try {
      $token = $token ? self::removeBearer($token) : self::getBearerToken();
      if (!$token) throw new Exception('Não foi possível identificar sua sessão, entre novamente.', 1);

      $tks = explode('.', $token);
      list(, $bodyb64,) = $tks;
      $payloadRaw = JWT::urlsafeB64Decode($bodyb64);
      $payloadArr = self::jsonDecode($payloadRaw, true);

      if (empty($payloadArr['session'])) throw new Exception('Não foi possível identificar sua sessão, entre novamente.', 2);

      $db = new DbApp($conn);
      $sql = "SELECT secret, CURRENT_TIMESTAMP > expires_at AS expired FROM user_sessions WHERE id = :id";
      $sessaoInfo = $db->queryPrimeiraLinha($sql, [':id' => $payloadArr['session']], [], ['expired']);

      if (!$sessaoInfo) throw new Exception('Não foi possível identificar sua sessão, entre novamente.', 3);
      if ($sessaoInfo['expired']) throw new Exception('A sessão expirou.', 4);

      JWT::decode($token, new Key($sessaoInfo['secret'], 'HS256'));

      $sql = "UPDATE user_sessions SET expires_at = CURRENT_TIMESTAMP + INTERVAL ttl SECOND WHERE id = :id";
      $db->update($sql, [':id' => $payloadArr['session']]);

      return $payloadArr;
    } catch (Exception $e) {
      if ($exception) throw $e;
      HttpHelper::erroJson(410, $e->getMessage(), $e->getCode(), ['payload' => $payloadArr ?? null]);
    }
  }
}
