<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\StoreUserRequestRequest;
use App\Http\Requests\User\CancelUserRequestRequest;
use App\Http\Requests\User\StoreAddPackageRequest;
use App\Http\Requests\User\StoreOutboundRequest;
use App\Http\Requests\User\UpdateUserRequestRequest;
use App\Services\User\UserRequestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserRequestController extends UserBaseController
{
    protected $requestService;

    public function __construct(UserRequestService $requestService)
    {
        parent::__construct();
        $this->requestService = $requestService;
    }

    /**
     * Display a listing of current user request.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        try {
            $input = $request->only('type', 'status');
            $items = $this->requestService->index($input);
            return view('user.request.index', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display a detail of current user request.
     *
     * @param  int  $userRequestId
     * @return \Illuminate\Contracts\View\View
     */
    public function show($userRequestId)
    {
        try {
            $items = $this->requestService->show($userRequestId);
            return view('user.request.show', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Show the form to create a new user request.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $items = $this->requestService->create();
            return view('user.request.create', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Store a new user request.
     *
     * @param  App\Http\Requests\User\StoreUserRequestRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequestRequest $request)
    {
        try {
            $this->requestService->store($request->all());
            return redirect()->route('requests.index')->with('success', "Create new request successed");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->route('requests.index')->with('fail', "Create new request failed");
        }
    }

    public function createAddPackage()
    {
        try {
            $items = $this->requestService->create();
            return view('user.request.create_add_package', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function storeAddPackage(StoreAddPackageRequest $request)
    {
        try {
            $params = $request->all();
            $res = $this->requestService->storeAddPackage($params);
            return view('user.request.confirm_add_package', $res)->with('success', "Create new request successed");

            // return redirect()->route('requests.index')->with('success', "Create new request successed");
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function createSkuAddPackage()
    {

    }

    public function createOutbound()
    {
        try {
            $items = $this->requestService->outbound();
            return view('user.request.create_outbound', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Store a new user request.
     *
     * @param  App\Http\Requests\User\StoreOutboundRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOutbound(StoreOutboundRequest $request)
    {
        try {
            $res = $this->requestService->storeOutbound($request->all());
            return view('user.request.confirm_outbound', $res)->with('success', "Create new request successed");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->route('requests.index')->with('fail', "Create new request failed");
        }
    }


    public function edit($userRequestId)
    {
        try {
            $items = $this->requestService->edit($userRequestId);
            return view('user.request.edit', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Update the given request.
     *
     * @param  App\Http\Requests\User\UpdateUserRequestRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequestRequest $request)
    {
        try {
            $this->requestService->update($request->all());
            return redirect()->route('requests.index')->with('success', "Update new request successed");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->route('requests.index')->with('fail', "Update new request failed");
        }
    }

    /**
     * Cancel a request.
     *
     * @param  App\Http\Requests\User\CancelUserRequestRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel(CancelUserRequestRequest $request)
    {
        try {
            $this->requestService->cancel($request->only('id'));
            return redirect()->back()->with('success', "Cancel request success");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', "Cancel request failed");
        }
    }

    /**
     * Mark notify of done request as read.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function notify($id)
    {
        try {
            $notification = $this->requestService->notify($id);
            if ($notification) {
                $notification->markAsRead();
                return redirect()->route('requests.show', $notification->data['user_request_id']);
            }

            return redirect()->route('dashboard');
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Notification list
     *
     */
    public function notifyList()
    {
        try {
            $notifications = $this->requestService->notifyList();
            return $notifications;
        } catch(Exception $e) {
            Log::error($e);
            return null;
        }
    }
}
