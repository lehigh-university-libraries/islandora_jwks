<?php

declare(strict_types=1);

namespace Drupal\islandora_jwks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Islandora JWKS routes.
 */
class IslandoraJwksController extends ControllerBase {

  /**
   * Builds the JWKS response.
   */
  public function __invoke(): JsonResponse {
    $keyPath = '/var/run/s6/container_environment/JWT_PUBLIC_KEY';

    $publicKey = $this->readPublicKey($keyPath);
    if ($publicKey === NULL) {
      return new JsonResponse(['error' => 'Public key not found'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $opensslKey = openssl_pkey_get_public($publicKey);
    if ($opensslKey === FALSE) {
      return new JsonResponse(['error' => 'Invalid RSA public key'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $details = openssl_pkey_get_details($opensslKey);
    if (!$details || !isset($details['rsa'])) {
      return new JsonResponse(['error' => 'Failed to extract RSA key details'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $jwks = [
      'keys' => [
        [
          'kty' => 'RSA',
          'kid' => sha1($publicKey),
          'use' => 'sig',
          'alg' => 'RS256',
          'n'   => $this->base64UrlEncode($details['rsa']['n']),
          'e'   => $this->base64UrlEncode($details['rsa']['e']),
        ],
      ],
    ];

    return new JsonResponse($jwks, Response::HTTP_OK);
  }

  /**
   * Reads the public key from disk.
   */
  protected function readPublicKey(string $path): ?string {
    return is_readable($path) ? trim(file_get_contents($path)) : NULL;
  }

  /**
   * Encodes data in base64 URL format.
   */
  private function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

}
