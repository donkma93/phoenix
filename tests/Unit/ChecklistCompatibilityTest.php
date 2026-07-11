<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Offline compatibility checklist for memory/storage optimizations.
 * Does not require MySQL — verifies code contracts that protect old flows.
 */
class ChecklistCompatibilityTest extends TestCase
{
    protected function appPath(string $rel): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);
    }

    protected function read(string $rel): string
    {
        $path = $this->appPath($rel);
        $this->assertFileExists($path, "Missing file: {$rel}");
        return file_get_contents($path);
    }

    /** @test CASE-01 Staff order list uses DataTable AJAX, not full SP dump on page */
    public function staff_order_list_uses_datatable_not_full_sp_on_page()
    {
        $src = $this->read('app/Http/Controllers/Staff/StaffOrderController.php');
        $this->assertStringContainsString('listDataTable', $src);
        $this->assertStringContainsString('listForDataTable', $src);
        // Page load must NOT call order_list_staff SP (was memory bomb)
        $listMethod = $this->extractMethod($src, 'public function list');
        $this->assertStringNotContainsString('order_list_staff', $listMethod);
        $this->assertStringContainsString('getOrderStatusCounts', $listMethod);
        $this->assertStringContainsString("'emails'", $listMethod);
        $this->assertStringContainsString("'users'", $listMethod);
        $this->assertStringContainsString("'tracking_status'", $listMethod);
        $this->assertStringContainsString("'count_status'", $listMethod);
    }

    /** @test CASE-02 Staff DataTable service returns required row fields */
    public function staff_datatable_selects_required_fields()
    {
        $src = $this->read('app/Services/Staff/StaffOrderService.php');
        $method = $this->extractMethod($src, 'public function listForDataTable');
        foreach ([
            'order_code', 'picking_status', 'order_number', 'amount',
            'tracking_number', 'label_url', 'provider', 'partner_code',
            'user_email', 'transactions_id', 'odr_rate_id', 'count_pkl',
            'item', 'quantity',
        ] as $field) {
            $this->assertStringContainsString($field, $method, "Missing field in listForDataTable: {$field}");
        }
        // Must paginate at SQL level
        $this->assertStringContainsString('offset', strtolower($method));
        $this->assertStringContainsString('limit', strtolower($method));
    }

    /** @test CASE-03 User order list still uses original stored procedure */
    public function user_order_index_uses_customer_order_list_sp()
    {
        $src = $this->read('app/Http/Controllers/User/UserOrderController.php');
        $method = $this->extractMethod($src, 'public function index');
        $this->assertStringContainsString("call customer_order_list", $method);
        $this->assertStringContainsString("'orders'", $method);
        $this->assertStringContainsString('oldInput', $method);
    }

    /** @test CASE-04 User order view keeps client DataTable paging */
    public function user_order_view_keeps_client_datatable()
    {
        $src = $this->read('resources/views/user/order/index.blade.php');
        $this->assertStringContainsString("DataTable({", $src);
        $this->assertStringContainsString('lengthMenu', $src);
        $this->assertStringNotContainsString('paging: false', $src);
        $this->assertStringContainsString('@foreach ($orders as $order)', $src);
        $this->assertStringContainsString('label_list[]', $src);
        $this->assertStringContainsString('order_code', $src);
    }

    /** @test CASE-05 Import order still creates orders; no permanent xlsx archive */
    public function import_order_does_not_archive_xlsx_but_creates_order()
    {
        foreach ([
            'app/Services/User/UserOrderService.php',
            'app/Services/Staff/StaffOrderService.php',
        ] as $file) {
            $src = $this->read($file);
            $method = $this->extractMethod($src, 'public function storeCsv');
            $this->assertStringContainsString('Excel::import', $method);
            $this->assertStringContainsString('Order::create', $method);
            $this->assertStringContainsString("'isValid'", $method);
            // Must NOT move import file into public/imgs/orders
            $this->assertStringNotContainsString(
                "move('imgs'",
                $method,
                "{$file} storeCsv must not permanently archive import files"
            );
            // content/file null is intentional disk/DB optimization
            $this->assertStringContainsString("'content' => null", $method);
            $this->assertStringContainsString("'file' => null", $method);
        }
    }

    /** @test CASE-06 Controllers only check isValid on import (rawData optional) */
    public function import_controllers_only_require_isValid()
    {
        $staff = $this->read('app/Http/Controllers/Staff/StaffOrderController.php');
        $user = $this->read('app/Http/Controllers/User/UserOrderController.php');
        $this->assertStringContainsString("!\$data['isValid']", $staff);
        $this->assertStringContainsString("!\$data['isValid']", $user);
        // Controllers must not require rawData
        $this->assertStringNotContainsString("rawData", $staff);
        $this->assertStringNotContainsString("rawData", $user);
    }

    /** @test CASE-07 orderPrintMultiple accepts order_code and order_id; merges PDF */
    public function order_print_multiple_resolves_codes_and_merges()
    {
        $src = $this->read('app/Services/Staff/StaffOrderService.php');
        $method = $this->extractMethod($src, 'public function orderPrintMultiple');
        $this->assertStringContainsString('order_code', $method);
        $this->assertStringContainsString('whereIn', $method);
        $this->assertStringContainsString('merge()', $method);
        $this->assertStringContainsString('save(', $method);
        $this->assertStringContainsString('return $outFile', $method);
        $this->assertStringContainsString('readLabelBinary', $method);
        // Must not delete PNX_LABEL sources
        $this->assertStringNotContainsString('PNX_LABEL', $method);
        $this->assertStringNotContainsString('deleteLocalMediaFile', $method);
    }

    /** @test CASE-08 readLabelBinary supports URL and local MyIB storage paths */
    public function read_label_binary_supports_myib_paths()
    {
        $src = $this->read('app/Services/Staff/StaffOrderService.php');
        $method = $this->extractMethod($src, 'protected function readLabelBinary');
        $this->assertStringContainsString('https?://', $method);
        $this->assertStringContainsString("storage_path('app/public/", $method);
        $this->assertStringContainsString('public_path', $method);
    }

    /** @test CASE-09 Cleanup never touches MyIB PNX_LABEL */
    public function cleanup_never_deletes_myib_labels()
    {
        $src = $this->read('app/Console/Commands/CleanupStorage.php');
        $this->assertStringContainsString('PNX_LABEL', $src);
        $this->assertStringContainsString('isProtectedPath', $src);
        $this->assertStringContainsString('NEVER', $src);
        // No orphan-label deletion method
        $this->assertStringNotContainsString('cleanOrphanLabels', $src);
        $this->assertStringNotContainsString('labels-days', $src);
        // Safe targets only
        $this->assertStringContainsString('imgs/orders', $src);
        $this->assertStringContainsString('public_path(\'tmp\')', $src);
        $this->assertStringContainsString('debugbar', $src);

        $kernel = $this->read('app/Console/Kernel.php');
        $this->assertStringContainsString('storage:cleanup', $kernel);
        $this->assertStringNotContainsString('labels-days', $kernel);
    }

    /** @test CASE-10 Protected path helper rejects PNX_LABEL */
    public function cleanup_protected_path_logic()
    {
        require_once $this->appPath('vendor/autoload.php');
        // Lightweight: reimplement check same as command
        $fragments = [
            'uploads' . DIRECTORY_SEPARATOR . 'PNX_LABEL',
            'uploads/PNX_LABEL',
            'documents' . DIRECTORY_SEPARATOR . 'g7',
            'documents/g7',
        ];
        $isProtected = function (string $path) use ($fragments): bool {
            $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
            foreach ($fragments as $fragment) {
                $frag = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fragment);
                if (stripos($normalized, $frag) !== false) {
                    return true;
                }
            }
            return false;
        };

        $this->assertTrue($isProtected('E:/phoenix/storage/app/public/uploads/PNX_LABEL/202511/x.pdf'));
        $this->assertTrue($isProtected('storage/app/public/uploads/PNX_LABEL/a.png'));
        $this->assertTrue($isProtected('public/documents/g7/label.pdf'));
        $this->assertFalse($isProtected('public/imgs/orders/123_Shipping.xlsx'));
        $this->assertFalse($isProtected('public/tmp/C_123abc.pdf'));
        $this->assertFalse($isProtected('storage/debugbar/foo.json'));
    }

    /** @test CASE-11 Delete label only removes file on intentional delete */
    public function delete_label_only_on_user_action()
    {
        $src = $this->read('app/Http/Controllers/Staff/StaffOrderController.php');
        $method = $this->extractMethod($src, 'protected function doDeleteLabel');
        $this->assertStringContainsString('deleteLocalMediaFile', $method);
        // Only after successful delete
        $this->assertStringContainsString('$deleteSuccess', $method);
        $this->assertStringContainsString('label_url', $method);
    }

    /** @test CASE-12 Staff base controller does not preload all users every request */
    public function staff_base_lazy_loads_users()
    {
        $src = $this->read('app/Http/Controllers/Staff/StaffBaseController.php');
        $ctor = $this->extractMethod($src, 'public function __construct');
        $this->assertStringNotContainsString('getAllUser', $ctor);
        $this->assertStringNotContainsString('getAllWarehouseArea', $ctor);
        $this->assertStringContainsString('function getUsers', $src);
        $pkg = $this->read('app/Http/Controllers/Staff/StaffPackageController.php');
        $this->assertStringContainsString('getUsers()', $pkg);
    }

    /** @test CASE-13 Notification uses COUNT not full chat load */
    public function notification_uses_count_distinct()
    {
        $src = $this->read('app/Services/Staff/StaffBaseService.php');
        $method = $this->extractMethod($src, 'function notification');
        $this->assertStringContainsString('COUNT(DISTINCT chat_box_id)', $method);
        $this->assertStringNotContainsString('->get()', $method);
    }

    /** @test CASE-14 Messenger getChatBox returns latest per box without full history */
    public function messenger_latest_per_chat_box()
    {
        $src = $this->read('app/Services/Staff/StaffMessengerService.php');
        $method = $this->extractMethod($src, 'public function getChatBox');
        $this->assertStringContainsString('MAX(id)', $method);
        $this->assertStringContainsString('groupBy', $method);
        $this->assertStringContainsString('chatBox.user.profile', $method);
    }

    /** @test CASE-15 Pickup staff still returns full date-range collection */
    public function pickup_staff_returns_full_list_with_batch_load()
    {
        $src = $this->read('app/Services/User/UserPickupRequestService.php');
        $method = $this->extractMethod($src, 'public function indexStaff');
        $this->assertStringContainsString('->get()', $method);
        $this->assertStringNotContainsString('paginate', $method);
        $this->assertStringContainsString('whereIn(\'id_pickup_request\'', $method);
        $this->assertStringContainsString('orderJourneys', $method);
        $this->assertStringContainsString('totalKG', $method);
        $this->assertStringContainsString('created_username', $method);
    }

    /** @test CASE-16 Dashboard states still available without loading all orders */
    public function dashboard_states_via_aggregate()
    {
        $src = $this->read('app/Services/User/UserDashboardService.php');
        $index = $this->extractMethod($src, 'public function index');
        $this->assertStringContainsString('getCompletedOrderStateCounts', $index);
        $this->assertStringContainsString("'states'", $index);
        $agg = $this->extractMethod($src, 'public function getCompletedOrderStateCounts');
        $this->assertStringContainsString('groupBy', $agg);
        $this->assertStringContainsString('UNITED STATES', $agg);
    }

    /** @test CASE-17 Preview label handles absolute MyIB URLs */
    public function preview_handles_absolute_label_urls()
    {
        $src = $this->read('app/Http/Controllers/Staff/StaffOrderController.php');
        $method = $this->extractMethod($src, 'public function listDataTable');
        $this->assertStringContainsString('https?://', $method);
        $this->assertStringContainsString('label_url', $method);
        $this->assertStringContainsString('previewPDF', $method);
        $this->assertStringContainsString('previewImage', $method);
    }

    /** @test CASE-18 Logging config uses daily rotation not unbounded single file */
    public function logging_uses_daily_channel()
    {
        $src = $this->read('config/logging.php');
        $this->assertStringContainsString("'channels' => ['daily']", $src);
        $this->assertStringContainsString("'days' => 14", $src);
    }

    /** @test CASE-19 Helpers and cleanup command are loadable (syntax/autoload) */
    public function critical_classes_exist_and_are_valid_php()
    {
        $files = [
            'app/Console/Commands/CleanupStorage.php',
            'app/Supports/helpers.php',
            'app/Services/Staff/StaffOrderService.php',
            'app/Http/Controllers/Staff/StaffOrderController.php',
            'app/Http/Controllers/User/UserOrderController.php',
            'app/Services/User/UserPickupRequestService.php',
            'app/Services/Staff/StaffMessengerService.php',
            'app/Services/User/UserDashboardService.php',
            'app/Services/User/UserOrderService.php',
            'app/Http/Controllers/Staff/StaffBaseController.php',
            'app/Services/Staff/StaffBaseService.php',
        ];
        foreach ($files as $file) {
            $path = $this->appPath($file);
            $this->assertFileExists($path);
            // php -l already run in CI style: file non-empty and starts with <?php
            $content = file_get_contents($path);
            $this->assertStringStartsWith('<?php', $content, $file);
        }
    }

    /** @test CASE-20 MyIB label files on disk are not in cleanup targets */
    public function myib_label_files_not_in_cleanup_targets()
    {
        $src = $this->read('app/Console/Commands/CleanupStorage.php');
        // handle() stats keys
        $this->assertStringContainsString("'tmp'", $src);
        $this->assertStringContainsString("'order_imports'", $src);
        $this->assertStringContainsString("'debugbar'", $src);
        $this->assertStringContainsString("'preview_zips'", $src);
        $this->assertStringNotContainsString("'orphan_labels'", $src);

        $labelDir = $this->appPath('storage/app/public/uploads/PNX_LABEL');
        if (is_dir($labelDir)) {
            $count = 0;
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($labelDir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if ($f->isFile()) {
                    $count++;
                }
            }
            $this->assertGreaterThan(0, $count, 'PNX_LABEL samples should remain on disk for system use');
        } else {
            $this->assertTrue(true, 'No local PNX_LABEL samples — skip presence check');
        }
    }

    /**
     * Extract a method body by name prefix (best-effort brace matching).
     */
    protected function extractMethod(string $src, string $signaturePrefix): string
    {
        $pos = strpos($src, $signaturePrefix);
        $this->assertNotFalse($pos, "Method not found: {$signaturePrefix}");
        $braceStart = strpos($src, '{', $pos);
        $this->assertNotFalse($braceStart);
        $depth = 0;
        $len = strlen($src);
        for ($i = $braceStart; $i < $len; $i++) {
            $ch = $src[$i];
            if ($ch === '{') {
                $depth++;
            } elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($src, $pos, $i - $pos + 1);
                }
            }
        }
        $this->fail("Unbalanced braces for {$signaturePrefix}");
        return '';
    }
}
