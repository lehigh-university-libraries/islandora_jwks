<?php

declare(strict_types=1);

namespace Drupal\Tests\islandora_jwks\Unit\Controller;

use Drupal\islandora_jwks\Controller\IslandoraJwksController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Tests the IslandoraJwksController.
 *
 * @group islandora_jwks
 */
final class IslandoraJwksControllerTest extends TestCase {

  /**
   * Tests the JWKS response.
   */
  public function testJwksResponse(): void {
    // Sample RSA public key (PEM format)
    $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6uK3nozywVaRCAB3FHdR
ZNHunSZvN/c31QimZAqQMGxj7JrGh1LF8JRX+XAQ+CJcPD9r6xXjKSS1Gqa2Os2w
ARr/9abIwG5QeNsrJ8GMt3Z/WICnNeaFAkUVviwKWcA61iFJWvTDAuI0hCaxArRK
sk0BfFSMh+4u3JAdD9tUxUx6AAUXUCdtPyluaBd53wuB0r9xRlPnDw6I9QHfKK80
Xrrsu1PYATgrsy69stzCln3KlO5Oxc6O8OjMdjC2D2c3HmsO4CKPvvaVuaow/a9P
a3SNje4UXN+/1xUfQskxafP8CKVSr8xxtwzSureiskb5/98moAiutpUtp15yyAm0
rwIDAQAB
-----END PUBLIC KEY-----
EOD;

    $mock = $this->getMockBuilder(IslandoraJwksController::class)
      ->onlyMethods(['readPublicKey'])
      ->getMock();

    $mock->method('readPublicKey')
      ->willReturn($publicKey);

    $response = $mock->__invoke();

    $this->assertInstanceOf(JsonResponse::class, $response);

    $jwks = json_decode($response->getContent(), TRUE);
    $this->assertArrayHasKey('keys', $jwks);
    $this->assertCount(1, $jwks['keys']);
    $this->assertEquals('RSA', $jwks['keys'][0]['kty']);
    $this->assertEquals('RS256', $jwks['keys'][0]['alg']);
    $this->assertEquals('sig', $jwks['keys'][0]['use']);
    $this->assertEquals('df52de600a824a30ba84a4745b0ebcebf7edca44', $jwks['keys'][0]['kid']);
    $this->assertArrayHasKey('n', $jwks['keys'][0]);
    $this->assertArrayHasKey('e', $jwks['keys'][0]);
  }

}
