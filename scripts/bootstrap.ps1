Param()
$ErrorActionPreference = 'Stop'

function Need($cmd) {
  if (-not (Get-Command $cmd -ErrorAction SilentlyContinue)) {
    Write-Host "[x] 請先安裝 $cmd" -ForegroundColor Red
    exit 1
  }
}
Need docker

# Docker Engine 檢查
try { docker version | Out-Null } catch {
  Write-Host "[x] Docker Engine 未啟動，請先打開 Docker Desktop" -ForegroundColor Red
  exit 1
}

$Root = (Resolve-Path "$PSScriptRoot\..\").Path
$Src = Join-Path $Root 'src'
if (-not (Test-Path $Src)) { New-Item -ItemType Directory -Force -Path $Src | Out-Null }

Write-Host "[i] 預先拉映像..."
docker compose -f "$Root\docker-compose.yml" pull --ignore-pull-failures | Out-Null

Write-Host "[i] 啟動 db / redis..."
docker compose -f "$Root\docker-compose.yml" up -d db redis
Start-Sleep -Seconds 5

Write-Host "[i] 啟動 app / web / node..."
docker compose -f "$Root\docker-compose.yml" up -d app web node

# 若 src 不是 Laravel 專案，先備份再建立新專案（完全避免 '!' 和 ()）
$artisanPath = Join-Path $Src 'artisan'
if (-not (Test-Path $artisanPath)) {
  $hasFiles = (Get-ChildItem $Src -Force -ErrorAction SilentlyContinue | Where-Object { $_.Name -ne '.' -and $_.Name -ne '..' }).Count -gt 0
  if ($hasFiles) {
    $bk = Join-Path $Root ("src_backup_" + (Get-Date -Format "yyyyMMdd_HHmmss"))
    Write-Host "[i] 偵測到 src 不是空的，備份到: $bk"
    Rename-Item -Path $Src -NewName $bk
    New-Item -ItemType Directory -Force -Path $Src | Out-Null
  }

  Write-Host "[+] 建立 Laravel 專案..."
  docker compose -f "$Root\docker-compose.yml" run --rm app sh -lc "if command -v composer >/dev/null 2>&1; then :; else curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; fi; if [ -f artisan ]; then :; else composer create-project laravel/laravel . --prefer-dist --no-interaction --no-progress && cp .env.example .env && php artisan key:generate; fi"

}

Write-Host "[+] 寫入 .env（強制 MySQL + 停用 sqlite）..."
docker compose -f "$Root\docker-compose.yml" exec -T app sh -lc "set -e; \
  if [ ! -f .env ]; then cp .env.example .env; fi; \
  sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env; \
  sed -i 's/^DB_HOST=.*/DB_HOST=db/' .env; \
  sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env; \
  sed -i 's/^DB_DATABASE=.*/DB_DATABASE=app/' .env; \
  sed -i 's/^DB_USERNAME=.*/DB_USERNAME=app/' .env; \
  sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=secret/' .env; \
  sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env; \
  rm -f database/database.sqlite || true; \
  php artisan config:clear; php artisan optimize:clear"

Write-Host "[+] 安裝 Breeze (Vue + Tailwind)..."
docker compose -f "$Root\docker-compose.yml" exec -T app sh -lc "if [ -f artisan ]; then composer require laravel/breeze --dev && php artisan breeze:install vue; fi"

Write-Host "[+] 安裝前端並建置..."
docker compose -f "$Root\docker-compose.yml" exec -T node sh -lc "if [ -f package.json ]; then rm -rf node_modules package-lock.json; npm pkg set devDependencies.vite='^7' devDependencies.@vitejs/plugin-vue='^6' devDependencies.laravel-vite-plugin='^2'; npm install --no-fund --no-audit || npm install --legacy-peer-deps --no-fund --no-audit; npm run build || true; fi"

Write-Host "[+] 執行資料庫遷移..."
docker compose -f "$Root\docker-compose.yml" exec -T app sh -lc "if [ -f artisan ]; then php artisan migrate || true; fi"

Write-Host ""
Write-Host "完成" -ForegroundColor Green
Write-Host "- 後端:  http://localhost:8080"
Write-Host "- HMR:   make npm-dev  或  docker compose exec node sh -lc 'npm run dev -- --host 0.0.0.0 --port 5173'"
Write-Host "- DB:    host=localhost port=3306 db=app user=app pwd=secret"
