<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\ChangePasswordRequest;

class AuthController extends Controller
{
    protected $request;
    protected $repo;
    protected $user;
    protected $activity;
    protected $module = 'user';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, AuthRepository $repo, UserRepository $user, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->user = $user;
        $this->activity = $activity;
        $this->middleware('prohibited.test.mode')->only('changePassword');
    }

    /**
     * Used to authenticate user
     * @post ("/api/auth/login")
     * @param ({
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     * })
     * @return authentication token
     */
    public function login(LoginRequest $request)
    {
        $auth = $this->repo->auth($this->request->all());

        $auth_user       = $auth['user'];
        $token           = $auth['token'];

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $auth_user->id,
            'user_id'   => $auth_user->id,
            'activity'  => 'logged_in'
        ]);

        $reload = (config('app.locale') != cache('locale') || config('config.direction') != cache('direction')) ? 1 : 0;

        return $this->success([
            'message'         => trans('auth.logged_in'),
            'token'           => $token,
            'user'            => $auth_user,
            'reload'          => $reload
        ]);
    }

    /**
     * Used to check user authenticated or not
     * @post ("/api/auth/check")
     * @return Response
     */
    public function check()
    {
        return $this->success($this->repo->check());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->success(auth()->user());
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->success(auth()->refresh());
    }

    /**
     * Used to logout user
     * @post ("/api/auth/logout")
     * @return Response
     */
    public function logout()
    {
        $auth_user = auth()->user();

        \Cache::forget('direction');
        \Cache::forget('locale');
        
        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $auth_user->id,
            'user_id'   => $auth_user->id,
            'activity'  => 'logged_out'
        ]);

        auth()->logout();

        return $this->success(['message' => trans('auth.logged_out')]);
    }

    /**
     * Used to create user
     * @post ("/api/auth/register")
     * @param ({
     *      @Parameter("first_name", type="text", required="true", description="First Name of User"),
     *      @Parameter("last_name", type="text", required="true", description="Last Name of User"),
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     *      @Parameter("password_confirmation", type="password", required="true", description="Confirm Password of User"),
     *      @Parameter("tnc", type="checkbox", required="optional", description="Accept Terms & Conditions"),
     * })
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
        $this->repo->validateRegistrationStatus();

        $new_user = $this->user->create($this->request->all(), 1);

        return $this->success(['message' => trans('auth.account_created')]);
    }

    /**
     * Used to activate new user
     * @get ("/api/auth/activate/{token}")
     * @param ({
     *      @Parameter("token", type="string", required="true", description="Activation Token of User"),
     * })
     * @return Response
     */
    public function activate($activation_token)
    {
        $this->repo->activate($activation_token);

        return $this->success(['message' => trans('auth.account_activated')]);
    }

    /**
     * Used to request password reset token for user
     * @post ("/api/auth/password")
     * @param ({
     *      @Parameter("email", type="email", required="true", description="Registered Email of User"),
     * })
     * @return Response
     */
    public function password(PasswordRequest $request)
    {
        $this->repo->password($this->request->all());

        return $this->success(['message' => trans('passwords.sent')]);
    }

    /**
     * Used to validate user password
     * @post ("/api/auth/validate-password-reset")
     * @param ({
     *      @Parameter("token", type="string", required="true", description="Reset Password Token"),
     * })
     * @return Response
     */
    public function validatePasswordReset()
    {
        $reset = $this->repo->validateResetPasswordToken(request('token'));

        return $this->success(['message' => '', 'email'=>$reset->email]);
    }

    /**
     * Used to reset user password
     * @post ("/api/auth/reset")
     * @param ({
     *      @Parameter("token", type="string", required="true", description="Reset Password Token"),
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="New Password of User"),
     *      @Parameter("password_confirmation", type="password", required="true", description="New Confirm Password of User"),
     * })
     * @return Response
     */
    public function reset(ResetPasswordRequest $request)
    {
        $this->repo->reset($this->request->all());

        return $this->success(['message' => trans('passwords.reset')]);
    }

    /**
     * Used to change user password
     * @post ("/api/change-password")
     * @param ({
     *      @Parameter("current_password", type="password", required="true", description="Current Password of User"),
     *      @Parameter("new_password", type="password", required="true", description="New Password of User"),
     *      @Parameter("new_password_confirmation", type="password", required="true", description="New Confirm Password of User"),
     * })
     * @return Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->repo->validateCurrentPassword(request('current_password'));

        $this->repo->resetPassword(request('new_password'));

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => \Auth::user()->id,
            'sub_module' => 'password',
            'activity'   => 'resetted'
        ]);

        return $this->success(['message' => trans('passwords.change')]);
    }

    /**
     * Used to verify password during Screen Lock
     * @post ("/api/auth/lock")
     * @param ({
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     * })
     * @return Response
     */
    public function lock(LoginRequest $request)
    {
        $this->repo->validateCurrentPassword(request('password'));

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => \Auth::user()->id,
            'sub_module' => 'screen',
            'activity'   => 'unlocked'
        ]);

        return $this->success(['message' => trans('auth.lock_screen_verified')]);
    }
}
