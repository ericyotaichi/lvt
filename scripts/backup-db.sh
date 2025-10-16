#!/bin/bash
# 数据库备份脚本

BACKUP_DIR="./backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/app_${TIMESTAMP}.sql"

# 创建备份目录
mkdir -p "$BACKUP_DIR"

# 备份数据库
echo "正在备份数据库..."
docker exec lvt_db mysqldump -u app -psecret app > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "备份成功: $BACKUP_FILE"
    # 压缩备份文件
    gzip "$BACKUP_FILE"
    echo "备份已压缩: ${BACKUP_FILE}.gz"
    
    # 删除7天前的备份
    find "$BACKUP_DIR" -name "app_*.sql.gz" -mtime +7 -delete
    echo "已清理7天前的备份文件"
else
    echo "备份失败！"
    exit 1
fi

