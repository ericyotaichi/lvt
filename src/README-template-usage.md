# 樣板貼上與啟動

1. 將本壓縮包內容解壓到 **Laravel 專案根目錄**（與 `artisan` 同層）。
2. 在 `app/Http/Kernel.php` 的 `$routeMiddleware` 新增：
   ```php
   'lead.completed' => \App\Http\Middleware\EnsureLeadCompleted::class,
   ```
3. 依序執行：
   ```bash
   docker compose exec app php artisan migrate --seed
   docker compose exec node sh -lc "npm install && npm run build"
   # HMR：docker compose exec node sh -lc "npm run dev -- --host 0.0.0.0 --port 5173"
   ```
4. 在前端頁面引入元件：`LeadForm.vue`, `GatedOverlay.vue`, `IdentitySelector.vue`, `CardGrid.vue`。
5. 受保護路徑：`/applications/*`, `/cases/*` 需先送出 `/lead` 表單以解鎖檢視。
