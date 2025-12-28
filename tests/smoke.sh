#!/usr/bin/env bash
set -euo pipefail

# Minimalny test integracyjny (smoke): start compose, curl health, stop compose.
# Używane w CI.

export APP_PORT="${APP_PORT:-8080}"

docker compose up -d --build

# czekamy aż app wstanie (db ma healthcheck; app depends_on)
for i in {1..30}; do
  if curl -fsS "http://localhost:${APP_PORT}/health.php" >/dev/null; then
    echo "OK: health endpoint works"
    docker compose down
    exit 0
  fi
  sleep 2
done

echo "FAIL: app did not become healthy in time"
docker compose logs --no-color
docker compose down
exit 1
