<#
Purpose: Pull latest from main and redeploy Docker container.
Usage: Run in the repo root in PowerShell.
#>

param(
    [string]$Branch = "main",
    [switch]$NoBuild,
    [switch]$Prune,
    [switch]$Verbose
)

$ErrorActionPreference = "Stop"

function Write-Info($msg) { Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Ok($msg) { Write-Host "[OK]   $msg" -ForegroundColor Green }
function Write-Warn($msg) { Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function Write-Err($msg) { Write-Host "[ERR]  $msg" -ForegroundColor Red }

# Ensure we are in repo root where docker-compose.yml exists
if (-not (Test-Path -Path "docker-compose.yml")) {
    Write-Err "docker-compose.yml not found. Run this in repo root."
    exit 1
}

# Confirm git is available
if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Err "git not found in PATH. Install Git and retry."
    exit 1
}

# Confirm docker compose is available
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Err "docker not found in PATH. Install Docker Desktop/Engine."
    exit 1
}

Write-Info "Fetching latest from origin/$Branch"
git fetch origin $Branch

Write-Info "Checking out $Branch"
git checkout $Branch

Write-Info "Pulling latest commits"
git pull --ff-only origin $Branch

if ($LASTEXITCODE -ne 0) {
    Write-Err "Git pull failed. Resolve and rerun."
    exit $LASTEXITCODE
}

Write-Info "Building assets (SCSS, Tailwind, JS)"
npm run build

if ($LASTEXITCODE -ne 0) {
    Write-Err "Asset build failed. Check npm dependencies."
    exit $LASTEXITCODE
}

if ($Prune) {
    Write-Info "Pruning dangling images and containers"
    docker system prune -f
}

if ($NoBuild) {
    Write-Info "Starting containers without rebuild"
    docker compose up -d
} else {
    Write-Info "Rebuilding images and restarting containers"
    docker compose up -d --build
}

if ($LASTEXITCODE -ne 0) {
    Write-Err "Docker compose failed. Check logs."
    exit $LASTEXITCODE
}

Write-Ok "Deployment complete. Current container status:"
docker compose ps

Write-Info "Tail logs (Ctrl+C to stop)"
docker compose logs -f --tail=200