<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminPackageGroupService;
use App\Http\Requests\Admin\SearchByDateRequest;
use App\Http\Requests\Admin\DeletePackageGroupRequest;
use App\Http\Requests\Admin\UpdateGroupRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPackageGroupController extends AdminBaseController
{
    protected $packageGroupService;

    public function __construct(AdminPackageGroupService $packageGroupService)
    {
        parent::__construct();
        $this->packageGroupService = $packageGroupService;
    }

    /**
     * Display a listing package.
     *
     * @param App\Http\Requests\Admin\SearchByDateRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function list(SearchByDateRequest $request)
    {
        $input = $request->only('onlyDeleted', 'status', 'email', 'name', 'barcode', 'startDate', 'endDate');
        $packages = $this->packageGroupService->list($input);

        return view('admin.package-group.list', $packages);
    }

    /**
     * Display a detail of package.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        $packages = $this->packageGroupService->detail($id);

        return view('admin.package-group.detail', $packages);
    }

    /**
     * Update user for group
     *
     * @return App\Http\Requests\Admin\UpdateGroupRequest $request
     */
    public function update(UpdateGroupRequest $request)
    {
        try {
            $input = $request->only('id', 'email');
            $isSuccess = $this->packageGroupService->update($input);

            if($isSuccess == true) {
                return redirect()->back()->with('success', "Update package group successfully!");
            }

            return redirect()->back()->with('fail', "Update package group fail!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update package group fail!");
        }
    }

    /**
     * Delete package group.
     *
     * @param App\Http\Requests\Admin\DeletePackageGroupRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function delete(DeletePackageGroupRequest $request)
    {
        try {
            $input = $request->only('id');
            $isSuccess = $this->packageGroupService->delete($input);

            if($isSuccess == true) {
                return redirect()->back()->with('success', "Update package group successfully!");
            }

            return redirect()->back()->with('fail', "Update package group fail!  This group is in new or inprogress request!");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', "Update package group fail!");
        }
    }

    /**
     * Compare package group and product.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function compare(Request $request)
    {
        try {
            $this->packageGroupService->compare();

            return redirect()->back()->with('success', "Compare successful!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Compare fail!");
        }
    }

    /**
     * Display a listing of package group history.
     *
     * @param App\Http\Requests\Admin\SearchByDateRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function history(SearchByDateRequest $request)
    {
        $input = $request->only('type', 'name', 'previous_name', 'barcode', 'previous_barcode', 'start_date', 'end_date', 'previous_email', 'email', 'staff');
        $histories = $this->packageGroupService->history($input);

        return view('admin.package-group-history.list', $histories);
    }

    /**
     * Display package group history detail.
     *
     * @param App\Http\Requests\Admin\SearchByDateRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function historyDetail(Request $request, $id)
    {
        $history = $this->packageGroupService->historyDetail($id);

        return view('admin.package-group-history.detail', $history);
    }
}
