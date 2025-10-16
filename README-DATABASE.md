# 数据库持久化配置说明

## 数据持久化

数据库已配置为使用 Docker 命名卷（named volume），确保数据在容器重启或删除后仍然保留。

### 配置说明

- **命名卷**: `db_data`
- **位置**: Docker 管理的卷，不会因为容器删除而丢失
- **自动重启**: 容器配置了 `restart: unless-stopped`，确保容器自动重启

## 备份数据库

### Windows (PowerShell)

```powershell
.\scripts\backup-db.ps1
```

### Linux/Mac

```bash
chmod +x scripts/backup-db.sh
./scripts/backup-db.sh
```

备份文件会保存在 `backups/` 目录，格式为 `app_YYYYMMDD_HHMMSS.sql.gz`

## 恢复数据库

### Windows (PowerShell)

```powershell
.\scripts\restore-db.ps1 backups\app_20251112_151509.sql
```

### Linux/Mac

```bash
chmod +x scripts/restore-db.sh
./scripts/restore-db.sh backups/app_20251112_151509.sql
```

## 查看数据库卷信息

```bash
docker volume inspect lvt-starter-v2_db_data
```

## 手动备份

```bash
docker exec lvt_db mysqldump -u app -psecret app > backups/manual_backup.sql
```

## 手动恢复

```bash
docker exec -i lvt_db mysql -u app -psecret app < backups/manual_backup.sql
```

## 注意事项

1. **定期备份**: 建议每天自动备份数据库
2. **备份保留**: 备份脚本会自动删除7天前的备份文件
3. **数据安全**: 数据库卷数据存储在 Docker 管理的卷中，不会因为 `docker-compose down` 而丢失
4. **完全删除**: 如果需要完全删除数据库数据，需要删除卷：
   ```bash
   docker-compose down -v
   ```

