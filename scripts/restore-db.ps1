# 数据库恢复脚本 (PowerShell)
# 使用方法: .\restore-db.ps1 backups\app_20251112_151509.sql

param(
    [Parameter(Mandatory=$true)]
    [string]$BackupFile
)

if (-not (Test-Path $BackupFile)) {
    Write-Host "错误: 备份文件不存在: $BackupFile" -ForegroundColor Red
    exit 1
}

# 如果是 .zip 文件，先解压
if ($BackupFile -match '\.zip$') {
    Write-Host "正在解压备份文件..."
    $TempFile = $BackupFile -replace '\.zip$', '.sql'
    Expand-Archive -Path $BackupFile -DestinationPath (Split-Path $BackupFile) -Force
    $BackupFile = $TempFile
}

Write-Host "正在恢复数据库..."
Get-Content $BackupFile | docker exec -i lvt_db mysql -u app -psecret app

if ($LASTEXITCODE -eq 0) {
    Write-Host "数据库恢复成功！" -ForegroundColor Green
} else {
    Write-Host "数据库恢复失败！" -ForegroundColor Red
    exit 1
}

