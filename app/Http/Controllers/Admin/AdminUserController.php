<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminUserService;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\CheckUserRequest;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminUserController extends AdminBaseController
{
    protected $userService;

    public function __construct(AdminUserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     * Display list user
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $input = $request->only('role', 'email', 'isVerify', 'onlyDeleted');
            $users = $this->userService->list($input);

            return view('admin.user.list', $users);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Show create new user page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function new(Request $request)
    {
        return view('admin.user.new');
    }

    /**
     * Show user profile
     *
     * @param  int  $userId
     * @return \Illuminate\Contracts\View\View
     */
    public function profile(Request $request, $id)
    {
        $userInfo = $this->userService->getUserInfo($id);

        return view('admin.user.profile', $userInfo);
    }

    /**
     * Create user
     *
     * @param  App\Http\Requests\Admin\CreateUserRequest  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function createUser(CreateUserRequest $request)
    {
        try {
            $input = $request->only('password', 'email', 'role', 'partner_code');
            $rs = $this->userService->create($input);

            if (isset($rs['status']) && $rs['status'] === 'error') {
                $request->flash();
                return redirect()->back()->with('error', $rs['message']);
            } else {
                return redirect()->back()->with('success', "Create user successfully!");
            }
        } catch(Exception $e) {
            Log::error($e);
            $request->flash();

            return redirect()->back()->with('error', "Create user fail!");
        }
    }

    /**
     * Update user
     *
     * @param  App\Http\Requests\Admin\CheckUserRequest  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function updateUser(CheckUserRequest $request)
    {
        try {
            $input = $request->only('id', 'delete', 'membership', 'verify');
            if(isset($input['delete'])) {
                $this->userService->deleteUser($input);
            } elseif (isset($input['membership'])){
                $this->userService->setOrRemoveMembership($input);
            } elseif (isset($input['verify'])) {
                $this->userService->setOrRemoveVerify($input);
            }

            return redirect()->back()->with('success', "Update user successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('error', "Update user fail!");
        }
    }
}
