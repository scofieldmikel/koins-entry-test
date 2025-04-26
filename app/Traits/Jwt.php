<?php

namespace App\Traits;

use Exception;
use App\Models\User;
use App\Models\JwtAccessToken;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Firebase\JWT\JWT as JwtLibrary;
use Firebase\JWT\Key as JwtLibraryKey;

trait Jwt {
  public static function encodeJwtToken(User $user): string
  {
    try {
      $key = config('app.key');
      $issuedAt = new \DateTimeImmutable();
      $expireAt = $issuedAt->modify('+2 days')->getTimestamp();

      /* Create The JWT Payload */
      $payload = [
        'iss' => "imsme_auth_service",
        'iat' => $issuedAt->getTimestamp(),
        'nbf' => $issuedAt->getTimestamp(),
        'exp' => $expireAt,
        'user' => new UserResource($user)
      ];

      /* Encode The Payload */
      $jwtToken = JwtLibrary::encode($payload, $key, 'HS256');

      /* Write To the DB Token Logs */
      Self::logJwtToken($user->id, $jwtToken);

      return $jwtToken;
    } catch (Exception $e) {
      Log::error($e->getMessage(), $e->getTrace());
      return null;
    }
  }

  public static function decodeJwtToken(string $token): object
  {
    try {
      $decodedJwt = JwtLibrary::decode($token, new JwtLibraryKey(config('app.key'), 'HS256'));

      return $decodedJwt;
    } catch (Exception $e) {
      Log::error($e->getMessage(), $e->getTrace());
      return null;
    }
  }

  public static function logJwtToken(int $userId, string $jwtToken) :JwtAccessToken
  {
    $jwtAccessToken = JwtAccessToken::create([
      'user_id' => $userId,
      'access_token' => $jwtToken,
      'revoked' => false,
      'expires_at' => now()->addDays(2),
      'created_at' => now(),
      'updated_at' => now()
    ]);

    return $jwtAccessToken;
  }
}
