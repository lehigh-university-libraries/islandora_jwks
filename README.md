## INTRODUCTION

The Islandora JWKS module provides a JWKS URI for Islandora's JWT tokens at `/oauth/discovery/keys`.

## REQUIREMENTS

- drupal/jwt
- drupal/islandora

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION
- Ensure your JWT public key is available at `/var/run/s6/container_environment/JWT_PUBLIC_KEY`. If using [isle-buildkit](https://github.com/islandora-devops/isle-buildkit) to run your Islandora site this will be handled automatically for you.

## MAINTAINERS

Current maintainers for Drupal 10:

- Joe Corall (joecorall) - https://www.drupal.org/u/joecorall
