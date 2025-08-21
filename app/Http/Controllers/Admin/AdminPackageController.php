<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminPackageService;
use App\Http\Requests\Admin\SearchByDateRequest;
use App\Http\Requests\Admin\DeletePackageRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPackageController extends AdminBaseController
{
    protected $packageService;

    public function __construct(AdminPackageService $packageService)
    {
        parent::__construct();
        $this->packageService = $packageService;
    }

    /**
     * Display a listing of package history.
     *
     * @param App\Http\Requests\Admin\SearchByDateRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function history(SearchByDateRequest $request)
    {
        $input = $request->only('onlyDeleted', 'status', 'name', 'warehouse', 'barcode', 'startDate', 'endDate', 'type');
        $packages = $this->packageService->history($input);

        return view('admin.package.history', $packages);
    }

    /**
     * Display a listing package.
     *
     * @param App\Http\Requests\Admin\SearchByDateRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function list(SearchByDateRequest $request)
    {
        $input = $request->only('onlyDeleted', 'status', 'email', 'warehouse', 'startDate', 'endDate', 'barcode', 'previous_status');
        $packages = $this->packageService->list($input);

        return view('admin.package.list', $packages);
    }

    /**
     * Display a detail of package.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        $packages = $this->packageService->detail($id);

        return view('admin.package.detail', $packages);
    }

    /**
     * Display a detail of history.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function historyDetail(Request $request, $id)
    {
        $history = $this->packageService->historyDetail($id);

        return view('admin.package.history-detail', $history);
    }

    /**
     * Delete package.
     *
     * @param App\Http\Requests\Admin\DeletePackageRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function delete(DeletePackageRequest $request)
    {
        try {
            $input = $request->only('id', 'user_id');
            $isSuccess = $this->packageService->delete($input);

            if($isSuccess == true) {
                return redirect()->back()->with('success', "Update package successfully!");
            }

            return redirect()->back()->with('fail', "Update package fail! This package in new or inprogress request!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update package fail!");
        }
    }
}
