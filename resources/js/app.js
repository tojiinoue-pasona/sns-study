import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// CSRF対策: すべてのfetch APIリクエストにCSRFトークンを自動付与
window.addEventListener('DOMContentLoaded', function() {
    // CSRFトークンを取得
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (csrfToken) {
        // fetch API のデフォルトヘッダーを設定
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            // POSTリクエストの場合、CSRFトークンを自動追加
            if (!options.method || options.method.toUpperCase() === 'POST' || 
                options.method.toUpperCase() === 'PUT' || 
                options.method.toUpperCase() === 'DELETE' ||
                options.method.toUpperCase() === 'PATCH') {
                
                options.headers = options.headers || {};
                options.headers['X-CSRF-TOKEN'] = csrfToken;
                options.headers['Content-Type'] = options.headers['Content-Type'] || 'application/json';
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
            }
            
            return originalFetch.call(this, url, options);
        };
        
        // XMLHttpRequest のデフォルトヘッダーも設定
        const originalXHROpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
            this._method = method;
            return originalXHROpen.call(this, method, url, async, user, password);
        };
        
        const originalXHRSend = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.send = function(data) {
            if (this._method && 
                (this._method.toUpperCase() === 'POST' || 
                 this._method.toUpperCase() === 'PUT' || 
                 this._method.toUpperCase() === 'DELETE' ||
                 this._method.toUpperCase() === 'PATCH')) {
                this.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                this.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            }
            return originalXHRSend.call(this, data);
        };
    }
});
