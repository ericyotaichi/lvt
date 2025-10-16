#!/bin/bash
# 数据库恢复脚本
# 使用方法: ./restore-db.sh backups/app_20251112_151509.sql

if [ -z "$1" ]; then
    echo "使用方法: $0 <备份文件路径>"
    echo "示例: $0 backups/app_20251112_151509.sql"
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "错误: 备份文件不存在: $BACKUP_FILE"
    exit 1
fi

# 如果是 .gz 文件，先解压
if [[ "$BACKUP_FILE" == *.gz ]]; then
    echo "正在解压备份文件..."
    gunzip -c "$BACKUP_FILE" > "${BACKUP_FILE%.gz}"
    BACKUP_FILE="${BACKUP_FILE%.gz}"
fi

echo "正在恢复数据库..."
docker exec -i lvt_db mysql -u app -psecret app < "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "数据库恢复成功！"
else
    echo "数据库恢复失败！"
    exit 1
fi

