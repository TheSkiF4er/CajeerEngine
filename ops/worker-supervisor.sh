#!/usr/bin/env bash
set -euo pipefail

QUEUE="${1:-default}"
BIN="${PHP_BIN:-php}"
CLI="${CLI_PATH:-$(cd "$(dirname "$0")/.." && pwd)/cli/cajeer2}"

echo "CajeerEngine worker supervisor queue=$QUEUE"
while true; do
  "$BIN" "$CLI" jobs:work "$QUEUE"
  echo "Worker exited. Restarting in 1s..."
  sleep 1
done
