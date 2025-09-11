# Laravel SNS学習アプリ - 完全ガイド

## 🚀 クイックスタート

```bash
# 環境構築
make setup

# デモ環境リセット（学習開始時に実行）
make demo-reset

# 開発サーバー起動
make demo-start
```

## 👤 デモユーザー

| Email | Password | Role | 説明 |
|-------|----------|------|------|
| admin@example.com | password | admin | 管理者（全投稿閲覧・削除可能） |
| alice@example.com | password | user | 投稿者（Public/Followers/Draft投稿あり） |
| bob@example.com | password | user | フォロワー（Alice follows, いいね済み） |

## 📚 学習シナリオ台本

### シナリオ1: 基本機能学習
```bash
# 1. ログイン・認証確認
# URL: http://localhost:8000/login
# alice@example.com でログイン

# 2. 投稿一覧確認
# URL: http://localhost:8000/posts
# → Public投稿のみ表示されることを確認

# 3. 投稿作成
# URL: http://localhost:8000/posts/create
# 新規投稿作成して各可視性レベルを試す

# 4. 投稿詳細・編集
# Alice投稿をクリック → 編集権限確認
# 自分の投稿のみ編集ボタン表示

# 5. いいね機能（AJAX）
# ❤️ボタンクリック → ページ遷移なしで更新
# ブラウザ開発者ツールでXHR確認

# 6. コメント機能
# 投稿にコメント追加・削除を試す
```

### シナリオ2: 権限・セキュリティ学習
```bash
# 1. 可視性レベル確認
# alice@example.com でログイン
# URL: http://localhost:8000/posts
# → Public + Followers投稿が表示

# ログアウト → 再度同じURL
# → Publicのみ表示（ゲストユーザー）

# 2. Draft投稿直叩きテスト（404確認）
# Alice Draft投稿IDを確認（例：ID=3）
# URL: http://localhost:8000/posts/3
# → 403 Forbidden （作成者以外はアクセス不可）

# 3. フォロー機能
# bob@example.com でログイン
# Alice投稿で「Follow」ボタンクリック
# → フォロワー数増加確認

# 4. 検索機能
# URL: http://localhost:8000/search
# キーワード「Laravel」で検索
# タグ「Technology」で絞り込み
```

### シナリオ3: セキュリティテスト
```bash
# 1. XSS攻撃テスト
# 投稿内容に以下を入力:
<script>alert('XSS攻撃')</script>
<img src="x" onerror="alert('XSS')">

# → エスケープされて安全に表示されることを確認

# 2. CSRF攻撃テスト
curl -X POST http://localhost:8000/posts \
  -H "Content-Type: application/json" \
  -d '{"body":"CSRF test"}'
# → 419 CSRF token mismatch エラー

# 3. SQL Injection攻撃テスト
# 検索画面で以下を入力:
%' OR 1=1 --
'; DROP TABLE posts; --

# → 検索結果なし（攻撃ブロック）
# storage/logs/laravel.log に攻撃試行ログ出力

# 4. セキュリティテスト実行
php artisan security:test
# → 各種攻撃パターンの検出確認
```

## 🔐 セキュリティ確認例

### XSS対策テスト
```html
<!-- 入力 -->
<script>alert('XSS')</script>
Hello <b>World</b>

<!-- 出力 (安全) -->
&lt;script&gt;alert('XSS')&lt;/script&gt;
Hello &lt;b&gt;World&lt;/b&gt;
```

### CSRF対策テスト
```bash
# トークンなし → 失敗
curl -X POST http://localhost:8000/posts \
  -d '{"body":"test"}'
# Response: 419 CSRF token mismatch

# 正しいトークン → 成功
curl -X POST http://localhost:8000/posts \
  -H "X-CSRF-TOKEN: [実際のトークン]" \
  -d '{"body":"test"}'
# Response: 200 OK
```

### SQL Injection対策テスト
```bash
# 危険な検索クエリ
curl "http://localhost:8000/search?q=%27%20OR%201%3D1%20--"
# → 結果なし + ログ記録

# 正常な検索クエリ
curl "http://localhost:8000/search?q=Laravel"
# → 正常な検索結果
```

## 📊 Notion用図表・チェックリスト

### Auth/Gate/Policy アーキテクチャ図
```
┌─────────────────┐
│   Browser       │
│   (User)        │
└────────┬────────┘
         │ HTTP Request
         ▼
┌─────────────────┐
│   Middleware    │
│   - auth        │
│   - guest       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Controller    │
│   $this->       │
│   authorize()   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Gate/Policy   │
│   - view()      │
│   - update()    │
│   - delete()    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Model/DB      │
│   - User        │
│   - Post        │
│   - Comment     │
└─────────────────┘
```

### セキュリティ対比表
| 脅威 | 対策前 | 対策後 | 実装箇所 |
|------|--------|--------|----------|
| XSS | `{{ $data }}` | `{{ e($data) }}` | Bladeテンプレート |
| CSRF | トークンなし | `@csrf` + Middleware | 全フォーム |
| SQLi | 生SQL | QueryBuilder + エスケープ | SearchController |
| 認可 | チェックなし | Gate/Policy | 全Controller |
| ログ | なし | 監査ログ | AuditLogService |

### 最終チェックリスト

#### 🔐 セキュリティ
- [x] XSS対策（エスケープ処理）
- [x] CSRF対策（トークン検証）
- [x] SQL Injection対策（クエリビルダー + エスケープ）
- [x] 認証・認可（Auth + Gate/Policy）
- [x] セキュリティヘッダー（CSP, X-Frame-Options等）
- [x] 監査ログ（操作履歴記録）

#### 📱 機能
- [x] ユーザー認証（Login/Register/Logout）
- [x] 投稿CRUD（作成・表示・更新・削除）
- [x] いいね機能（AJAX）
- [x] フォロー機能（AJAX）
- [x] コメント機能
- [x] 検索機能（キーワード・タグ）
- [x] 画像アップロード
- [x] 可視性制御（Public/Followers/Draft）

#### 🧪 テスト
- [x] 単体テスト環境
- [x] セキュリティテスト（`php artisan security:test`）
- [x] 手動テストシナリオ
- [x] デモデータ（学習用固定データ）

#### 📚 学習教材
- [x] 詳細ドキュメント
- [x] デモシナリオ台本
- [x] セキュリティ確認手順
- [x] Makefile（環境管理）
- [x] 監査ログ（学習確認用）

#### 🎯 本番準備
- [x] 環境変数設定
- [x] エラーハンドリング
- [x] ログ設定
- [x] パフォーマンス最適化（N+1クエリ対策）
- [x] キャッシュ戦略

## 🏆 学習達成目標

この学習アプリで以下の知識・スキルを習得できます：

### Laravel基礎
- MVC アーキテクチャ
- Eloquent ORM & Relationship
- Middleware & Service Container
- Blade テンプレート

### セキュリティ
- 認証・認可の仕組み
- XSS/CSRF/SQLi対策
- セキュリティヘッダー
- 監査ログ

### 実践的開発
- AJAX通信
- ファイルアップロード
- 検索機能実装
- パフォーマンス最適化

### 開発プロセス
- デバッグ技術
- テスト手法
- ログ分析
- セキュリティ監査

---

📝 **学習のポイント**: 各機能を実際に触りながら、背後の仕組みを理解することが重要です。ログを確認し、セキュリティテストを実行して、堅牢なWebアプリケーション開発の基礎を身につけましょう。
