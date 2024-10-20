<?php

namespace App\Provider;

use App\Exception\CriticalException;

class JWT {

  /**
   * Generate a token JWT
   * @param array $payload Body of the token
   * @param array{key: string, alg: ?string, exp: numeric} $options {"key": "Secret key", "alg": "Algorithm of the token (default = HS256)", "exp": "Time of the expiration in seconds"}
   * @return string Token JWT
   */
  static function encode(array $payload, array $options) {
    if (!$options['key'])
      throw new CriticalException('"Key" option encode JWT not defined', ['message' => '"Key" not defined', 'origin' => 'key']);

    if ($options['exp'])
      $payload['exp'] = time() + $options['exp'];

    if (!$options['alg'])
      $options['alg'] = 'HS256';

    $payload['iat'] = time();

    try {
      return \Firebase\JWT\JWT::encode($payload, $options['key'], $options['alg']);
    } catch (\Exception $err) {
      throw new CriticalException($err->getMessage());
    }
  }

  /**
   * Returns the body of the JWT token 
   * @param string $token Token JWT
   * @param array{key: string, alg: ?string} $options {"key": "Secret key", "alg": "Algorithm of the token (default = HS256)"}
   * @return object Payload token
   */
  static function decode(string $token, array $options) {
    if (!$options['key'])
      throw new CriticalException('"Key" option decode JWT not defined', ['message' => '"Key" not defined', 'origin' => 'key']);

    if (!$options['alg'])
      $options['alg'] = 'HS256';

    try {
      return \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($options['key'], $options['alg']));
    } catch (\Exception $err) {
      throw new CriticalException($err->getMessage());
    }
  }
}
