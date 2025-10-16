# 数据库备份脚本 (PowerShell)

$BackupDir = ".\backups"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupFile = "$BackupDir\app_$Timestamp.sql"

# 创建备份目录
if (-not (Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir | Out-Null
}

# 备份数据库
Write-Host "正在备份数据库..."
docker exec lvt_db mysqldump -u app -psecret app | Out-File -FilePath $BackupFile -Encoding utf8

if ($LASTEXITCODE -eq 0) {
    Write-Host "备份成功: $BackupFile"
    
    # 压缩备份文件（需要 7-Zip 或 PowerShell 5.0+）
    if (Get-Command Compress-Archive -ErrorAction SilentlyContinue) {
        Compress-Archive -Path $BackupFile -DestinationPath "$BackupFile.zip" -Force
        Remove-Item $BackupFile
        Write-Host "备份已压缩: $BackupFile.zip"
    }
    
    # 删除7天前的备份
    Get-ChildItem "$BackupDir\app_*.sql*" | Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-7) } | Remove-Item
    Write-Host "已清理7天前的备份文件"
} else {
    Write-Host "备份失败！" -ForegroundColor Red
    exit 1
}

