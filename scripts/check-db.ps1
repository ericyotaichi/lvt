# 数据库状态检查脚本 (PowerShell)

Write-Host "=== 数据库状态检查 ===" -ForegroundColor Cyan

# 检查容器状态
Write-Host "`n1. 检查数据库容器状态..." -ForegroundColor Yellow
$containerStatus = docker ps -a --filter "name=lvt_db" --format "{{.Status}}"
if ($containerStatus) {
    Write-Host "   容器状态: $containerStatus" -ForegroundColor Green
} else {
    Write-Host "   错误: 数据库容器不存在！" -ForegroundColor Red
    exit 1
}

# 检查卷状态
Write-Host "`n2. 检查数据库卷状态..." -ForegroundColor Yellow
$volumeInfo = docker volume inspect lvt-starter-v2_db_data 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "   卷已存在: lvt-starter-v2_db_data" -ForegroundColor Green
    $volumeJson = $volumeInfo | ConvertFrom-Json
    Write-Host "   创建时间: $($volumeJson.CreatedAt)" -ForegroundColor Gray
    Write-Host "   挂载点: $($volumeJson.Mountpoint)" -ForegroundColor Gray
} else {
    Write-Host "   警告: 数据库卷不存在！" -ForegroundColor Yellow
}

# 检查数据库连接
Write-Host "`n3. 检查数据库连接..." -ForegroundColor Yellow
$dbCheck = docker exec lvt_db mysql -uroot -proot -e "SELECT 1;" 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "   数据库连接正常" -ForegroundColor Green
} else {
    Write-Host "   错误: 无法连接到数据库！" -ForegroundColor Red
    Write-Host "   详细信息: $dbCheck" -ForegroundColor Red
    exit 1
}

# 检查数据库和表
Write-Host "`n4. 检查数据库和表..." -ForegroundColor Yellow
$tables = docker exec lvt_db mysql -uroot -proot app -e "SHOW TABLES;" 2>&1
if ($LASTEXITCODE -eq 0) {
    $tableCount = ($tables | Measure-Object -Line).Lines - 1
    Write-Host "   数据库 'app' 存在" -ForegroundColor Green
    Write-Host "   表数量: $tableCount" -ForegroundColor Green
    
    # 检查关键表的数据
    Write-Host "`n5. 检查关键表数据..." -ForegroundColor Yellow
    $products = docker exec lvt_db mysql -uroot -proot app -e "SELECT COUNT(*) as count FROM products;" 2>&1
    $carousel = docker exec lvt_db mysql -uroot -proot app -e "SELECT COUNT(*) as count FROM carousel_slides;" 2>&1
    $footers = docker exec lvt_db mysql -uroot -proot app -e "SELECT COUNT(*) as count FROM footers;" 2>&1
    $about = docker exec lvt_db mysql -uroot -proot app -e "SELECT COUNT(*) as count FROM about_pages;" 2>&1
    
    Write-Host "   products: $($products -replace 'count\s+', '')" -ForegroundColor Gray
    Write-Host "   carousel_slides: $($carousel -replace 'count\s+', '')" -ForegroundColor Gray
    Write-Host "   footers: $($footers -replace 'count\s+', '')" -ForegroundColor Gray
    Write-Host "   about_pages: $($about -replace 'count\s+', '')" -ForegroundColor Gray
} else {
    Write-Host "   错误: 无法访问数据库 'app'！" -ForegroundColor Red
    Write-Host "   详细信息: $tables" -ForegroundColor Red
    exit 1
}

Write-Host "`n=== 检查完成 ===" -ForegroundColor Cyan
Write-Host "`n提示: 使用 'docker-compose down' 不会删除数据（卷会保留）" -ForegroundColor Yellow
Write-Host "警告: 使用 'docker-compose down -v' 会删除所有数据！" -ForegroundColor Red

