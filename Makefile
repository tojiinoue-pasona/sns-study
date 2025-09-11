# Laravel SNS App Makefile
# 学習用デモ環境構築・管理

.PHONY: help demo-reset demo-start demo-stop setup install test security-test

# デフォルトターゲット
help:
	@echo "🚀 Laravel SNS App - 学習用コマンド"
	@echo ""
	@echo "📚 学習環境管理:"
	@echo "  demo-reset    - デモ環境をリセット（DB再構築・シーディング・キャッシュクリア）"
	@echo "  demo-start    - 開発サーバー起動"
	@echo "  demo-stop     - 開発サーバー停止"
	@echo ""
	@echo "🔧 セットアップ:"
	@echo "  setup         - 初回環境構築"
	@echo "  install       - 依存関係インストール"
	@echo ""
	@echo "🧪 テスト・セキュリティ:"
	@echo "  test          - 全テスト実行"
	@echo "  security-test - セキュリティテスト実行"
	@echo ""

# デモ環境リセット（学習用メインコマンド）
demo-reset:
	@echo "🔄 デモ環境をリセット中..."
	php artisan migrate:fresh --seed
	php artisan storage:link
	php artisan cache:clear
	php artisan view:clear  
	php artisan config:clear
	php artisan route:clear
	@echo "✅ デモ環境リセット完了！"
	@echo ""
	@echo "👤 デモユーザー:"
	@echo "  admin@example.com (password: password) - 管理者"
	@echo "  alice@example.com (password: password) - 一般ユーザー"
	@echo "  bob@example.com   (password: password) - 一般ユーザー"
	@echo ""
	@echo "🌐 起動: make demo-start"

# 開発サーバー起動
demo-start:
	@echo "🚀 開発サーバーを起動中..."
	@echo "URL: http://localhost:8000"
	php artisan serve

# 開発サーバー停止
demo-stop:
	@echo "⏹️  開発サーバーを停止中..."
	pkill -f "php artisan serve" || true
	@echo "✅ 停止完了"

# 初回セットアップ
setup:
	@echo "🔧 初回環境構築中..."
	cp .env.example .env || true
	composer install
	npm install
	php artisan key:generate
	touch database/database.sqlite
	$(MAKE) demo-reset
	npm run build
	@echo "✅ 初回セットアップ完了！"

# 依存関係インストール
install:
	composer install
	npm install

# テスト実行
test:
	@echo "🧪 テスト実行中..."
	php artisan test

# セキュリティテスト実行
security-test:
	@echo "🛡️ セキュリティテスト実行中..."
	php artisan security:test
