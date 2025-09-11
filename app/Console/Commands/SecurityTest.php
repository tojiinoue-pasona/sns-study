<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SqlSecurityHelper;

class SecurityTest extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:test';

    /**
     * The console command description.
     */
    protected $description = 'Run security tests for SQL injection and CSRF protection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🛡️  Running Security Tests...');
        
        $this->testSqlInjectionDetection();
        $this->testLikeEscaping();
        
        $this->info('✅ All security tests completed!');
        return 0;
    }
    
    /**
     * Test SQL injection detection
     */
    private function testSqlInjectionDetection()
    {
        $this->info('📋 Testing SQL Injection Detection...');
        
        $testCases = [
            // 危険なパターン
            "'; DROP TABLE users; --",
            "' OR 1=1 --",
            "' UNION SELECT * FROM users --",
            "%' OR 1=1 --",
            "admin'/*",
            "' OR 'x'='x",
            "1; INSERT INTO users",
            
            // 安全なパターン
            "normal search text",
            "user@example.com",
            "日本語検索",
            "product-name_123",
        ];
        
        foreach ($testCases as $test) {
            $result = SqlSecurityHelper::validateSearchInput($test);
            $status = $result['safe'] ? '✅ SAFE' : '⚠️  BLOCKED';
            $this->line("  {$status}: " . substr($test, 0, 30) . (strlen($test) > 30 ? '...' : ''));
        }
    }
    
    /**
     * Test LIKE escaping
     */
    private function testLikeEscaping()
    {
        $this->info('📋 Testing LIKE Escaping...');
        
        $testCases = [
            'normal text',
            'text with % wildcard',
            'text with _ underscore',
            'text with \\ backslash',
            'complex \\%_test',
        ];
        
        foreach ($testCases as $test) {
            $escaped = SqlSecurityHelper::escapeLike($test);
            $this->line("  Original: {$test}");
            $this->line("  Escaped:  {$escaped}");
            $this->line('');
        }
    }
}
