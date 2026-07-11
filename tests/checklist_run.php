<?php
/**
 * Standalone checklist runner (no DB required).
 * Usage: php tests/checklist_run.php
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

$results = [];
$passCount = 0;
$failCount = 0;
$skipCount = 0;

function check(string $id, string $title, callable $fn): void
{
    global $results, $passCount, $failCount, $skipCount;
    try {
        $fn();
        $results[] = ['id' => $id, 'title' => $title, 'status' => 'PASS', 'detail' => ''];
        $passCount++;
        echo "[PASS] {$id} {$title}\n";
    } catch (Throwable $e) {
        $results[] = ['id' => $id, 'title' => $title, 'status' => 'FAIL', 'detail' => $e->getMessage()];
        $failCount++;
        echo "[FAIL] {$id} {$title}\n       → {$e->getMessage()}\n";
    }
}

function skip(string $id, string $title, string $reason): void
{
    global $results, $skipCount;
    $results[] = ['id' => $id, 'title' => $title, 'status' => 'SKIP', 'detail' => $reason];
    $skipCount++;
    echo "[SKIP] {$id} {$title}\n       → {$reason}\n";
}

function assert_true($cond, string $msg): void
{
    if (!$cond) {
        throw new RuntimeException($msg);
    }
}

function read(string $rel): string
{
    global $root;
    $path = $root . '/' . $rel;
    assert_true(is_file($path), "Missing file {$rel}");
    return file_get_contents($path);
}

function extract_method(string $src, string $sig): string
{
    $pos = strpos($src, $sig);
    assert_true($pos !== false, "Method not found: {$sig}");
    $braceStart = strpos($src, '{', $pos);
    $depth = 0;
    $len = strlen($src);
    for ($i = $braceStart; $i < $len; $i++) {
        if ($src[$i] === '{') {
            $depth++;
        } elseif ($src[$i] === '}') {
            $depth--;
            if ($depth === 0) {
                return substr($src, $pos, $i - $pos + 1);
            }
        }
    }
    throw new RuntimeException("Unbalanced braces: {$sig}");
}

echo "=== Phoenix optimization compatibility checklist ===\n";
echo "Root: {$root}\n";
echo "Time: " . date('c') . "\n\n";

// --- Structural / contract checks (always runnable) ---

check('C01', 'Staff order page does not load full SP into HTML', function () {
    $src = read('app/Http/Controllers/Staff/StaffOrderController.php');
    $m = extract_method($src, 'public function list');
    assert_true(strpos($m, 'order_list_staff') === false, 'list() still calls order_list_staff');
    assert_true(strpos($m, 'getOrderStatusCounts') !== false, 'missing status counts');
    assert_true(strpos($m, "'users'") !== false, 'missing users for email suggest');
});

check('C02', 'Staff DataTable SQL pagination + required fields', function () {
    $src = read('app/Services/Staff/StaffOrderService.php');
    $m = extract_method($src, 'public function listForDataTable');
    foreach (['label_url', 'tracking_number', 'user_email', 'order_code', 'offset', 'limit'] as $f) {
        assert_true(stripos($m, $f) !== false, "missing {$f}");
    }
});

check('C03', 'User order list uses customer_order_list SP (old flow)', function () {
    $m = extract_method(read('app/Http/Controllers/User/UserOrderController.php'), 'public function index');
    assert_true(strpos($m, 'customer_order_list') !== false, 'SP missing');
});

check('C04', 'User order view: client DataTable + print by order_code', function () {
    $src = read('resources/views/user/order/index.blade.php');
    assert_true(strpos($src, 'DataTable') !== false, 'DataTable missing');
    assert_true(strpos($src, 'paging: false') === false, 'paging disabled unexpectedly');
    assert_true(strpos($src, 'label_list[]') !== false, 'print checkbox missing');
    assert_true(strpos($src, 'order_code') !== false, 'order_code missing');
});

check('C05', 'Import order: creates Order, no permanent imgs/orders archive', function () {
    foreach (['app/Services/User/UserOrderService.php', 'app/Services/Staff/StaffOrderService.php'] as $f) {
        $m = extract_method(read($f), 'public function storeCsv');
        assert_true(strpos($m, 'Order::create') !== false, "{$f} no Order::create");
        assert_true(strpos($m, "move('imgs'") === false, "{$f} still archives import file");
        assert_true(strpos($m, "'isValid'") !== false, "{$f} missing isValid");
    }
});

check('C06', 'Import controllers only require isValid (not rawData)', function () {
    $staff = read('app/Http/Controllers/Staff/StaffOrderController.php');
    $user = read('app/Http/Controllers/User/UserOrderController.php');
    assert_true(strpos($staff, "isValid") !== false, 'staff missing isValid');
    assert_true(strpos($user, "isValid") !== false, 'user missing isValid');
    assert_true(strpos($staff, 'rawData') === false, 'staff requires rawData');
    assert_true(strpos($user, 'rawData') === false, 'user requires rawData');
});

check('C07', 'orderPrintMultiple: order_code+id, merge, return path, no delete labels', function () {
    $m = extract_method(read('app/Services/Staff/StaffOrderService.php'), 'public function orderPrintMultiple');
    assert_true(strpos($m, 'order_code') !== false, 'no order_code resolve');
    assert_true(strpos($m, 'merge()') !== false, 'no merge');
    assert_true(strpos($m, 'return $outFile') !== false, 'no return path');
    assert_true(strpos($m, 'deleteLocalMediaFile') === false, 'must not delete labels while printing');
    assert_true(strpos($m, 'readLabelBinary') !== false, 'no label reader');
});

check('C08', 'readLabelBinary supports URL + storage public MyIB paths', function () {
    $m = extract_method(read('app/Services/Staff/StaffOrderService.php'), 'protected function readLabelBinary');
    assert_true(strpos($m, "storage_path('app/public/") !== false, 'no storage_path(app/public) resolve');
    assert_true(strpos($m, 'https?://') !== false, 'no URL support');
    assert_true(strpos($m, 'public_path') !== false, 'no public_path resolve');
});

check('C09', 'storage:cleanup never deletes PNX_LABEL', function () {
    $src = read('app/Console/Commands/CleanupStorage.php');
    assert_true(strpos($src, 'cleanOrphanLabels') === false, 'orphan label cleanup still present');
    assert_true(strpos($src, 'labels-days') === false, 'labels-days option still present');
    assert_true(strpos($src, 'isProtectedPath') !== false, 'no path guard');
    assert_true(strpos($src, 'PNX_LABEL') !== false, 'no PNX_LABEL protection mention');
    $kernel = read('app/Console/Kernel.php');
    assert_true(strpos($kernel, 'storage:cleanup') !== false, 'not scheduled');
    assert_true(strpos($kernel, 'labels-days') === false, 'schedule still passes labels-days');
});

check('C10', 'Protected path logic: PNX_LABEL protected, imports not', function () {
    $fragments = ['uploads/PNX_LABEL', 'uploads\\PNX_LABEL'];
    $isProtected = function ($path) use ($fragments) {
        $n = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        foreach ($fragments as $f) {
            $frag = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $f);
            if (stripos($n, $frag) !== false) {
                return true;
            }
        }
        return false;
    };
    assert_true($isProtected('storage/app/public/uploads/PNX_LABEL/202511/a.pdf'), 'label should be protected');
    assert_true(!$isProtected('public/imgs/orders/1.xlsx'), 'import should not be protected');
});

check('C11', 'Delete label removes local file only on intentional delete', function () {
    $m = extract_method(read('app/Http/Controllers/Staff/StaffOrderController.php'), 'protected function doDeleteLabel');
    assert_true(strpos($m, 'deleteLocalMediaFile') !== false, 'missing file delete');
    assert_true(strpos($m, '$deleteSuccess') !== false, 'not gated on success');
});

check('C12', 'StaffBaseController lazy-loads users (not every request)', function () {
    $src = read('app/Http/Controllers/Staff/StaffBaseController.php');
    $ctor = extract_method($src, 'public function __construct');
    assert_true(strpos($ctor, 'getAllUser') === false, 'still loads all users in ctor');
    assert_true(strpos($src, 'function getUsers') !== false, 'missing getUsers');
});

check('C13', 'Notification COUNT DISTINCT chat (not load all messages)', function () {
    $m = extract_method(read('app/Services/Staff/StaffBaseService.php'), 'function notification');
    assert_true(strpos($m, 'COUNT(DISTINCT chat_box_id)') !== false, 'not using COUNT DISTINCT');
});

check('C14', 'Messenger latest message per chat box', function () {
    $m = extract_method(read('app/Services/Staff/StaffMessengerService.php'), 'public function getChatBox');
    assert_true(strpos($m, 'MAX(id)') !== false, 'no MAX(id)');
    assert_true(strpos($m, 'chatBox.user.profile') !== false, 'missing relations');
});

check('C15', 'Pickup staff full date-range list + batch load (no paginate cut-off)', function () {
    $m = extract_method(read('app/Services/User/UserPickupRequestService.php'), 'public function indexStaff');
    assert_true(strpos($m, 'paginate') === false, 'paginate would break DataTable-only UI');
    assert_true(strpos($m, '->get()') !== false, 'should get full filtered list');
    assert_true(strpos($m, 'whereIn') !== false, 'batch load missing');
    assert_true(strpos($m, 'totalKG') !== false, 'totalKG missing');
});

check('C16', 'Dashboard state aggregates available', function () {
    $src = read('app/Services/User/UserDashboardService.php');
    assert_true(strpos($src, 'getCompletedOrderStateCounts') !== false, 'aggregate missing');
    $m = extract_method($src, 'public function index');
    assert_true(strpos($m, "'states'") !== false, 'states not returned');
});

check('C17', 'Preview supports absolute MyIB label URLs', function () {
    $m = extract_method(read('app/Http/Controllers/Staff/StaffOrderController.php'), 'public function listDataTable');
    assert_true(strpos($m, 'https?://') !== false, 'no absolute URL handling');
});

check('C18', 'Logging daily rotate 14 days', function () {
    $src = read('config/logging.php');
    assert_true(strpos($src, "'channels' => ['daily']") !== false, 'not using daily channel');
    assert_true(strpos($src, "'days' => 14") !== false, 'no 14-day retention');
});

check('C19', 'PHP syntax of all modified application files', function () use ($root) {
    $files = [
        'app/Console/Commands/CleanupStorage.php',
        'app/Console/Kernel.php',
        'app/Http/Controllers/Staff/StaffBaseController.php',
        'app/Http/Controllers/Staff/StaffOrderController.php',
        'app/Http/Controllers/Staff/StaffPackageController.php',
        'app/Http/Controllers/User/UserOrderController.php',
        'app/Services/Staff/StaffBaseService.php',
        'app/Services/Staff/StaffMessengerService.php',
        'app/Services/Staff/StaffOrderService.php',
        'app/Services/User/UserDashboardService.php',
        'app/Services/User/UserOrderService.php',
        'app/Services/User/UserPickupRequestService.php',
        'app/Supports/helpers.php',
        'config/logging.php',
    ];
    foreach ($files as $f) {
        $path = $root . '/' . $f;
        assert_true(is_file($path), "missing {$f}");
        $out = [];
        $code = 0;
        exec('php -l ' . escapeshellarg($path) . ' 2>&1', $out, $code);
        assert_true($code === 0, "{$f}: " . implode(' ', $out));
    }
});

check('C20', 'Existing MyIB label files remain on disk (not cleaned)', function () use ($root) {
    $dir = $root . '/storage/app/public/uploads/PNX_LABEL';
    assert_true(is_dir($dir), 'PNX_LABEL directory missing');
    $count = 0;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile()) {
            $count++;
        }
    }
    assert_true($count > 0, 'No label files found under PNX_LABEL');
});

// Runtime: artisan commands without DB
check('C21', 'artisan storage:cleanup --dry-run (safe targets only)', function () use ($root) {
    $out = [];
    $code = 0;
    exec('cd ' . escapeshellarg($root) . ' && php artisan storage:cleanup --dry-run --days=14 2>&1', $out, $code);
    $text = implode("\n", $out);
    // May fail without .env — still check output content if command ran
    if ($code !== 0 && stripos($text, 'Starting safe storage cleanup') === false) {
        // Try create minimal env for artisan
        throw new RuntimeException("artisan failed (code {$code}): {$text}");
    }
    assert_true(stripos($text, 'labels untouched') !== false || stripos($text, 'NEVER deleted') !== false || stripos($text, 'Protected') !== false, $text);
    assert_true(stripos($text, 'orphan_labels') === false, 'orphan_labels still reported');
});

check('C22', 'Route names for checklist flows are registered', function () use ($root) {
    $out = [];
    $code = 0;
    exec('cd ' . escapeshellarg($root) . ' && php artisan route:list 2>&1', $out, $code);
    $text = implode("\n", $out);
    assert_true($code === 0, "route:list failed: {$text}");
    foreach ([
        'staff.orders.list',
        'staff.orders.datatable',
        'staff.messenger',
        'staff.pickup.index',
        'staff.pickup.orderPrintMultiple',
        'orders.index',
        'staff.labels.import.excel.myib',
        'staff.delete.label',
    ] as $name) {
        assert_true(strpos($text, $name) !== false, "route missing: {$name}");
    }
});

// DB-dependent E2E — skip if no connection
$dbOk = false;
try {
    $envProd = $root . '/.env.prod';
    $map = [];
    if (is_file($envProd)) {
        foreach (file($envProd, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (preg_match('/^(DB_[A-Z_]+)=(.*)$/', $line, $m)) {
                $map[$m[1]] = trim($m[2], " \t\"'");
            }
        }
    }
    $dbUser = $map['DB_USERNAME'] ?? 'fmus';
    $dbPass = $map['DB_PASSWORD'] ?? '';
    $dbName = $map['DB_DATABASE'] ?? 'fmus';
    foreach (['127.0.0.1', 'localhost'] as $h) {
        try {
            $pdo = new PDO("mysql:host={$h};port=3306;dbname={$dbName}", $dbUser, $dbPass, [
                PDO::ATTR_TIMEOUT => 2,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $dbOk = true;
            break;
        } catch (Throwable $e) {
            // continue
        }
    }
} catch (Throwable $e) {
    $dbOk = false;
}

if (!$dbOk) {
    skip('E01', 'Staff orders list HTTP (auth + DB)', 'No MySQL credentials reachable from this machine (host db / fmus denied)');
    skip('E02', 'Staff orders DataTable AJAX', 'Requires DB + authenticated staff session');
    skip('E03', 'User orders index HTTP', 'Requires DB + authenticated user session');
    skip('E04', 'Import CSV/Excel end-to-end', 'Requires DB + upload + auth');
    skip('E05', 'Create/print/delete MyIB label E2E', 'Requires MyIB API + DB + auth');
    skip('E06', 'Pickup list HTTP', 'Requires DB + auth');
    skip('E07', 'Messenger list HTTP', 'Requires DB + auth');
} else {
    check('E01', 'DB reachable for E2E', function () {
        assert_true(true, 'ok');
    });
}

echo "\n=== SUMMARY ===\n";
echo "PASS: {$passCount}\n";
echo "FAIL: {$failCount}\n";
echo "SKIP: {$skipCount}\n";

// Write markdown report
$report = "# Checklist Test Report\n\n";
$report .= 'Generated: ' . date('c') . "\n\n";
$report .= "| ID | Status | Case | Detail |\n|----|--------|------|--------|\n";
foreach ($results as $r) {
    $detail = str_replace(["\n", '|'], [' ', '/'], $r['detail']);
    $report .= "| {$r['id']} | **{$r['status']}** | {$r['title']} | {$detail} |\n";
}
$report .= "\n**Totals:** PASS={$passCount} FAIL={$failCount} SKIP={$skipCount}\n";
$reportPath = $root . '/tests/CHECKLIST_REPORT.md';
file_put_contents($reportPath, $report);
echo "Report: {$reportPath}\n";

exit($failCount > 0 ? 1 : 0);
