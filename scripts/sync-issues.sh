#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CACHE_DIR="${ROOT_DIR}/.cache"
STAMP="$(date -u +"%Y-%m-%dT%H:%M:%SZ")"

OPEN_FILE="${CACHE_DIR}/issues-open.json"
CLOSED_FILE="${CACHE_DIR}/issues-closed.json"
META_FILE="${CACHE_DIR}/issues-meta.json"

if ! command -v gh >/dev/null 2>&1; then
  echo "Error: 'gh' no esta instal·lat."
  echo "Instal·la GitHub CLI: https://cli.github.com/"
  exit 1
fi

if ! gh auth status >/dev/null 2>&1; then
  echo "Error: no hi ha sessio de GitHub CLI."
  echo "Executa: gh auth login"
  exit 1
fi

mkdir -p "${CACHE_DIR}"

echo "Sincronitzant issues obertes..."
gh issue list \
  --state open \
  --limit 1000 \
  --json number,title,state,labels,assignees,milestone,updatedAt,createdAt,url \
  > "${OPEN_FILE}"

echo "Sincronitzant issues tancades..."
gh issue list \
  --state closed \
  --limit 1000 \
  --json number,title,state,labels,assignees,milestone,updatedAt,createdAt,url \
  > "${CLOSED_FILE}"

cat > "${META_FILE}" <<EOF
{
  "generated_at_utc": "${STAMP}",
  "open_file": ".cache/issues-open.json",
  "closed_file": ".cache/issues-closed.json"
}
EOF

echo "OK: issues sincronitzades en .cache/"
echo "- ${OPEN_FILE}"
echo "- ${CLOSED_FILE}"
echo "- ${META_FILE}"
