<?php
namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use App\Events\UserLogin;
use Illuminate\Support\Str;
use App\Notifications\Activated;
use App\Notifications\PasswordReset;
use App\Repositories\UserRepository;
use App\Notifications\PasswordResetted;
use App\Repositories\ConfigurationRepository;
use App\Repositories\LoginThrottleRepository;
use App\Repositories\Site;
use Illuminate\Validation\ValidationException;

class AuthRepository
{
    protected $user;
    protected $throttle;
    protected $config;
    protected $site;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $user,
        LoginThrottleRepository $throttle,
        ConfigurationRepository $config,
        Site $site
    ) {
        $this->user       = $user;
        $this->throttle   = $throttle;
        $this->config     = $config;
        $this->site     = $site;
    }

    /**
     * Authenticate an user.
     *
     * @param array $params
     * @return array
     */
    public function auth($params = array())
    {
        $this->throttle->validate();

        $token = $this->validateLogin($params);

        $auth_user = $this->user->findByEmail($params['email']);

        $this->validateStatus($auth_user);

        event(new UserLogin($auth_user));

        $auth_user = $this->user->findByEmail($auth_user->email);

        return [
            'token'           => $token,
            'user'            => $auth_user,
        ];
    }

    public function checkProfileAndPreference($auth_user)
    {
        $profile = $auth_user->Profile;

        if (!isset($profile) || $profile === '' || $profile === null) {
            $profile = new \App\Profile;
            $profile->user()->associate($auth_user);
            $profile->save();
        }

        $user_preference = $auth_user->UserPreference;

        if (!isset($user_preference) || $user_preference === '' || $user_preference === null) {
            $user_preference = new \App\UserPreference;
            $user_preference->user()->associate($auth_user);
            $user_preference->save();
        }

        $user_preference->color_theme = ($user_preference->color_theme) ? : config('config.color_theme');
        $user_preference->direction = ($user_preference->direction) ? : config('config.direction');
        $user_preference->locale = ($user_preference->locale) ? : config('config.locale');
        $user_preference->sidebar = ($user_preference->sidebar) ? : config('config.sidebar');
        $user_preference->save();
    }

    /**
     * Validate login credentials.
     *
     * @param array $params
     * @return auth token
     */
    public function validateLogin($params = array())
    {
        $email = isset($params['email']) ? $params['email'] : null;
        $password = isset($params['password']) ? $params['password'] : null;
        if (!$token = auth()->attempt(['email' => $email, 'password' => $password])) {
            $this->throttle->update();
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        $this->throttle->clearCache();

        return $token;
    }

    /**
     * Validate authenticated user status.
     *
     * @param authenticated user
     * @return null
     */
    public function validateStatus($auth_user)
    {
        if ($auth_user->status === 'pending_activation') {
            throw ValidationException::withMessages(['email' => trans('auth.pending_activation')]);
        }

        if ($auth_user->status === 'pending_approval') {
            throw ValidationException::withMessages(['email' => trans('auth.pending_approval')]);
        }

        if ($auth_user->status === 'disapproved') {
            throw ValidationException::withMessages(['email' => trans('auth.not_activated')]);
        }

        if ($auth_user->status === 'banned') {
            throw ValidationException::withMessages(['email' => trans('auth.account_banned')]);
        }

        if ($auth_user->status != 'activated') {
            throw ValidationException::withMessages(['email' => trans('auth.not_activated')]);
        }

        if (!$auth_user->hasPermissionTo('enable-login')) {
            throw ValidationException::withMessages(['email' => trans('auth.login_permission_disabled')]);
        }
    }

    /**
     * Validate auth token.
     *
     * @return array
     */
    public function check()
    {
        $public_config = config('system.public_config');
        $configuration = $this->config->getSelectedByName($public_config);

        $currency = \App\Currency::find($configuration['currency']);
        if (!$currency) {
            $currency = \App\Currency::create([
                'code'=>'USD',
                'name'=>'US Dollars',
                'symbol'=>'$',
                'symbol_position'=>'before',
            ]);
        }

        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            \Cache::forget('direction');
            \Cache::forget('locale');
            return [
                'authenticated' => false,
                'config' => $configuration,
                'currency'      => $currency,

            ];
        }

        $authenticated                  = true;
        $configuration                  = $this->config->getAllPublic();
        $configuration['post_max_size'] = getPostMaxSize();
        $configuration['pagination']    = config('system.pagination');
        $auth_user                      = $this->user->findOrFail(auth()->user()->id);
        $user_roles                     = $auth_user->getRoleNames();
        $permissions                    = $auth_user->getAllPermissions();
        $default_role                   = config('system.default_role');
        
      


        $data = [
            'authenticated' => $authenticated,
            'config'        => $configuration,
            'user'          => $auth_user,
            'user_roles'    => $user_roles,
            'permissions'   => $permissions,
            'default_role'  => $default_role,
            'currency'      => $currency,
        ];
        
       

        return $data;
    }

    /**
     * Check for registration availability.
     *
     * @return null
     */
    public function validateRegistrationStatus()
    {
        if (! config('config.registration')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for email verification availability.
     *
     * @return null
     */
    public function validateEmailVerificationStatus()
    {
        if (! config('config.email_verification')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for account approval availability.
     *
     * @return null
     */
    public function validateAccountApprovalStatus()
    {
        if (! config('config.account_approval')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for reset password availability.
     *
     * @return null
     */
    public function validateResetPasswordStatus()
    {
        if (! config('config.reset_password')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Activate user's account.
     *
     * @param string $activation token
     * @return null
     */
    public function activate($activation_token = null)
    {
        $this->validateRegistrationStatus();

        $this->validateEmailVerificationStatus();

        $user = $this->user->findByActivationToken($activation_token);

        if (!$user) {
            throw ValidationException::withMessages(['message' => trans('auth.invalid_token')]);
        }

        if ($user->status === 'activated') {
            throw ValidationException::withMessages(['message' => trans('auth.account_already_activated')]);
        }

        if ($user->status != 'pending_activation') {
            throw ValidationException::withMessages(['message' => trans('auth.invalid_token')]);
        }

        $user->status = (config('config.account_approval') ? 'pending_approval' : 'activated');
        $user->save();
        $user->notify(new Activated($user));
    }

    /**
     * Validate user for reset password.
     *
     * @param email $email
     * @return User
     */
    public function validateUserAndStatusForResetPassword($email = null)
    {
        $user = $this->user->findByEmail($email);

        if (! $user) {
            throw ValidationException::withMessages(['email' => trans('passwords.user')]);
        }

        if ($user->status != 'activated') {
            throw ValidationException::withMessages(['email' => trans('passwords.account_not_activated')]);
        }

        return $user;
    }

    /**
     * Request password reset token of user.
     *
     * @param array
     * @return null
     */
    public function password($params = array())
    {
        $this->validateResetPasswordStatus();

        $user = $this->validateUserAndStatusForResetPassword($params['email']);

        $token = Str::uuid();
        \DB::table('password_resets')->insert([
            'email' => $params['email'],
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $user->notify(new PasswordReset($user, $token));
    }

    /**
     * Validate reset password token.
     *
     * @param string $token
     * @param email $email
     * @return null
     */
    public function validateResetPasswordToken($token, $email = null)
    {
        if ($email) {
            $reset = \DB::table('password_resets')->where('email', '=', $email)->where('token', '=', $token)->first();
        } else {
            $reset = \DB::table('password_resets')->where('token', '=', $token)->first();
        }

        if (! $reset) {
            throw ValidationException::withMessages(['message' => trans('passwords.token')]);
        }

        if (date("Y-m-d H:i:s", strtotime($reset->created_at . "+".config('config.reset_password_token_lifetime')." minutes")) < date('Y-m-d H:i:s')) {
            throw ValidationException::withMessages(['email' => trans('passwords.token_expired')]);
        }

        return $reset;
    }

    /**
     * Reset password of user.
     *
     * @param array
     * @return null
     */
    public function reset($params = array())
    {
        $this->validateResetPasswordStatus();
    
        $user = $this->validateUserAndStatusForResetPassword($params['email']);

        $this->validateResetPasswordToken($params['token'], $params['email']);

        $this->resetPassword($params['password'], $user);

        \DB::table('password_resets')->where('email', '=', $params['email'])->where('token', '=', $params['token'])->delete();

        $user->notify(new PasswordResetted($user));
    }

    /**
     * Update user password.
     *
     * @param string $password
     * @param User $user
     * @return null
     */
    public function resetPassword($password, $user = null)
    {
        $user = ($user) ? : \Auth::user();
        $user->password = bcrypt($password);
        $user->save();
    }

    /**
     * Validate current password of user.
     *
     * @param string $password
     * @return null
     */
    public function validateCurrentPassword($password)
    {
        if (!\Hash::check($password, \Auth::user()->password)) {
            throw ValidationException::withMessages(['message' => trans('passwords.password_mismatch')]);
        }
    }
}
