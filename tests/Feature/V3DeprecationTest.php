<?php

namespace Tests\Feature;

use Tests\TestCase;

class V3DeprecationTest extends TestCase
{
    private string $tmpScript = '';
    private array $savedServer = [];
    private array $savedPost = [];

    protected function tearDown(): void
    {
        if ($this->tmpScript !== '' && file_exists($this->tmpScript)) {
            unlink($this->tmpScript);
        }
        $_SERVER = $this->savedServer;
        $_POST = $this->savedPost;
        parent::tearDown();
    }

    public function test_api_inc_php_sets_deprecation_header(): void
    {
        $content = file_get_contents(__DIR__ . '/../../admin/api/api.inc.php');
        $this->assertStringContainsString(
            'header("Deprecation: @',
            $content,
            'api.inc.php must set Deprecation header in Structured Fields Date format (RFC 9745)'
        );
    }

    public function test_api_inc_php_sets_sunset_header(): void
    {
        $content = file_get_contents(__DIR__ . '/../../admin/api/api.inc.php');
        $this->assertStringContainsString(
            'header("Sunset: ',
            $content,
            'api.inc.php must set Sunset header (RFC 8594)'
        );
        $this->assertStringContainsString(
            'UTC"',
            $content,
            'Sunset should use IMF-fixdate format with UTC timezone'
        );
    }

    public function test_api_inc_php_sets_link_header(): void
    {
        $content = file_get_contents(__DIR__ . '/../../admin/api/api.inc.php');
        $this->assertStringContainsString(
            'rel=\"successor-version\"',
            $content,
            'api.inc.php must set Link header pointing to successor version docs'
        );
    }

    public function test_api_inc_php_logs_v3_call(): void
    {
        $content = file_get_contents(__DIR__ . '/../../admin/api/api.inc.php');
        $this->assertStringContainsString(
            "Logger::info('v3 API endpoint called'",
            $content,
            'api.inc.php must log v3 endpoint calls'
        );
        $this->assertStringContainsString(
            "\$_SERVER['SCRIPT_NAME']",
            $content,
            'Logger entry must include endpoint name'
        );
        $this->assertStringContainsString(
            "\$_SERVER['HTTP_USER_AGENT']",
            $content,
            'Logger entry must include User-Agent'
        );
    }

    public function test_v3_signin_functionality_unchanged(): void
    {
        $pdo = $this->getPDO();

        $account = new \account($pdo);
        $result = $account->signin('nonexistent_user_' . bin2hex(random_bytes(4)), 'wrongpass');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result,
            'v3 signIn contract must include error key');
        $this->assertTrue($result['error'],
            'signIn with nonexistent user must return error=true');
    }

    public function test_v3_endpoint_produces_valid_json_under_subprocess(): void
    {
        $projectRoot = realpath(__DIR__ . '/../../');
        $this->tmpScript = sys_get_temp_dir() . '/flycash_v3_' . bin2hex(random_bytes(8)) . '.php';

        $body = '';
        $body .= '<?php' . "\n";
        $body .= 'chdir(' . var_export($projectRoot . '/admin/api/v3', true) . ');' . "\n";
        $body .= '$_SERVER["HTTPS"] = "on";' . "\n";
        $body .= '$_SERVER["REMOTE_ADDR"] = "127.0.0.1";' . "\n";
        $body .= '$_SERVER["SCRIPT_NAME"] = "/admin/api/v3/account.signIn.php";' . "\n";
        $body .= '$_SERVER["HTTP_USER_AGENT"] = "PHPUnit-Test/1.0";' . "\n";
        $body .= '$_POST = [' . "\n";
        $body .= '    "clientId" => "1",' . "\n";
        $body .= '    "username" => "nonexistent_v3_" . bin2hex(random_bytes(4)),' . "\n";
        $body .= '    "password" => "wrongpass",' . "\n";
        $body .= '];' . "\n";
        $body .= 'ob_start();' . "\n";
        $body .= 'require ' . var_export($projectRoot . '/admin/api/v3/account.signIn.php', true) . ';' . "\n";
        $body .= '$output = ob_get_clean();' . "\n";
        $body .= 'echo $output;' . "\n";

        file_put_contents($this->tmpScript, $body);

        $phpBin = PHP_BINARY;
        $cmd = sprintf('%s %s 2>&1', escapeshellarg($phpBin), escapeshellarg($this->tmpScript));
        exec($cmd, $outputLines, $exitCode);

        $output = implode("\n", $outputLines);

        $this->assertSame(0, $exitCode,
            'Sub-process should exit with code 0. Output: ' . $output);

        $json = json_decode($output, true);
        $this->assertIsArray($json, 'v3 endpoint must produce valid JSON');
        $this->assertArrayHasKey('error', $json,
            'v3 signIn response must include error key');
        $this->assertTrue($json['error'],
            'signIn with nonexistent user must return error=true');
    }
}
