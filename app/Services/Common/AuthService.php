<?php
namespace App\Services\Common;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\OrganizationAdmin;
use App\Models\Physician;
use App\Repositories\AuthRepository;
use App\Services\Sms\Facade\Sms;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $repos = [];

    public function __construct()
    {
        $this->repos['admin']              = new AuthRepository(Admin::class);
        $this->repos['employee']           = new AuthRepository(Employee::class);
        $this->repos['physician']          = new AuthRepository(Physician::class);
        $this->repos['organization_admin'] = new AuthRepository(OrganizationAdmin::class);
    }

    public function login($username): array
    {
        $data = identifyAndNormalizeUsername($username);
        if ($data['type'] === 'unknown') {
            return ['success' => false];
        }
        $accounts = [];
        foreach ($this->repos as $key => &$repo) {
            $account = $repo->findOne($data['type'], $data['value']);
            if ($account) {
                $accounts[$key] = $account;
            }
        }
        if (count($accounts) == 1 && array_key_exists('employee', $accounts)) {
            $otp = strval(rand(1000, 9999));
            if (config('app.env') !== 'production') {
                $otp = collect(str_split(substr($accounts['employee']->phone, -4)))
                    ->map(fn($digit) => (int) $digit)
                    ->implode('');
            } else {
                $name = $accounts['employee']->name ?? "کاربر";
                Sms::send("{$name} عزیز، خوش اومدی!
                            اینم کد ورودت: {$otp}
                            این کد رو در اختیار شخص دیگه‌ای نذار!
                            @dev.zeework.ir #{$otp}
                            لغو 11", $accounts['employee']->phone);
            }
            cache()->put($accounts['employee']->phone, $otp, now()->addMinutes(5));
        }
        return [
            'success' => true,
            'data'    => [
                'select' => (count($accounts) > 1) ? true : false,
                'key'    => count($accounts) == 1 ? (array_key_exists('employee', $accounts) ? 'otp' : 'password') : false,
                'roles'  => array_keys($accounts),
            ],
        ];

    }
    public function sendOtp($username): array
    {
        $data = identifyAndNormalizeUsername($username);
        if ($data['type'] === 'unknown') {
            return ['success' => false];
        }
        $account = $this->repos['employee']->findOne($data['type'], $data['value']);
        if (! $account) {
            return ['success' => false];
        }
        $otp = strval(rand(1000, 9999));
        if (config('app.env') !== 'production') {
            $otp = collect(str_split(substr($account->phone, -4)))
                ->map(fn($digit) => (int) $digit)
                ->implode('');
        } else {
            $name = $account->name ?? "کاربر";
            Sms::send("{$name} عزیز، خوش اومدی!
                            اینم کد ورودت: {$otp}
                            این کد رو در اختیار شخص دیگه‌ای نذار!
                            @dev.zeework.ir #{$otp}
                            لغو 11", $account->phone);
        }
        cache()->put($account->phone, $otp, now()->addMinutes(5));
        return [
            'success' => true,
            'data'    => [
                'otp_status' => 'sended',
            ],
        ];

    }
    public function Validate($username, $role, $pass)
    {
        $data = identifyAndNormalizeUsername($username);
        if ($data['type'] === 'unknown') {
            return ['success' => false];
        }
        $account = $this->repos[$role]->findOne($data['type'], $data['value']);
        if (! $account) {
            return ['success' => false];
        }
        switch ($role) {
            case 'employee':
                $otp = cache()->get($account->phone);
                if (config('app.env') !== 'production') {
                    $codeValidation = $otp == substr($account->phone, -4);
                } else {
                    $codeValidation = $otp == $pass;
                }
                if (! $codeValidation) {
                    return ['success' => false];
                }
                $token = $account->createToken('employee-token')->plainTextToken;
                return [
                    'success' => true,
                    'data'    => [
                        'token'   => $token,
                        'role'    => $role,
                        'account' => $account,
                    ],
                ];

            default:
                if (! Hash::check($pass, $account->password)) {
                    return ['success' => false];
                }
                $token = $account->createToken((string) $role . '_token')->plainTextToken;
                return [
                    'success' => true,
                    'data'    => [
                        'token'   => $token,
                        'role'    => $role,
                        'account' => $account,
                    ],
                ];
        }
    }
}
