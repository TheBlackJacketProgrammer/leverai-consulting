#!/usr/bin/env bash
# Purpose: Pull latest from main and redeploy Docker container.
# Usage: Run in the repo root: ./deploy.sh [--branch main] [--no-build] [--prune]

set -euo pipefail

BRANCH="main"
NO_BUILD=0
PRUNE=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --branch)
      BRANCH="$2"; shift 2;;
    --no-build)
      NO_BUILD=1; shift;;
    --prune)
      PRUNE=1; shift;;
    *)
      echo "Unknown option: $1"; exit 1;;
  esac
done

info() { echo -e "\033[36m[INFO]\033[0m $*"; }
ok()   { echo -e "\033[32m[OK]  \033[0m $*"; }
err()  { echo -e "\033[31m[ERR] \033[0m $*"; }

if [[ ! -f docker-compose.yml ]]; then
  err "docker-compose.yml not found. Run this in repo root."
  exit 1
fi

command -v git >/dev/null 2>&1 || { err "git not found"; exit 1; }
command -v docker >/dev/null 2>&1 || { err "docker not found"; exit 1; }



# Install PHP dependencies
if [[ -f "composer.json" ]] && command -v composer >/dev/null 2>&1; then
    info "Installing PHP dependencies"
    composer install --no-dev --optimize-autoloader --quiet
fi

# Install Node dependencies
info "Installing npm dependencies"
# Optimized npm install (forcing devDependencies for build)
npm install --include=dev --no-audit --no-fund --quiet

info "Fixing permissions on local binaries"
chmod +x node_modules/.bin/* || true

info "Building assets (SCSS, Tailwind, JS)"
npm run build

# Docker Operations
if [[ "$PRUNE" -eq 1 ]]; then
  info "Pruning dangling images and containers"
  docker system prune -f
fi

if [[ "$NO_BUILD" -eq 1 ]]; then
  info "Starting containers without rebuild"
  docker compose up -d
else
  info "Rebuilding images and restarting containers"
  # .dockerignore ensures this is fast by excluding node_modules
  docker compose up -d --build
fi

info "Restarting containers to pick up new JavaScript bundle"
docker compose restart

ok "Deployment complete. Current container status:"
docker compose ps
