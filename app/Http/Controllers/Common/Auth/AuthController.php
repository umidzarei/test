<?php
namespace App\Http\Controllers\Common\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Common\AuthOtpRequest;
use App\Http\Requests\Common\AuthValidateRequest;
use App\Http\Requests\Common\LoginRequest;
use App\Services\Common\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $service;
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Initiate login process or request OTP",
     *     description="Accepts username (email or mobile). Returns roles if multiple, or key for next step (otp/password).",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials for login",
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     * @param LoginRequest $request     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login($request->username);
        if (!$result['success']) {
            return response()->apiResult(null, 401, [__('messages.login_failed')]);

        }
        return response()->apiResult($result['data']);
    }
    /**
     * @OA\Post(
     *     path="/api/auth/otp",
     *     tags={"Auth"},     *     summary="Request OTP for a given username",
     *     description="Sends an OTP if the user (typically employee) is found.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Username for OTP request",
     *         @OA\JsonContent(ref="#/components/schemas/AuthOtpRequest")
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     * @param AuthOtpRequest $request
     * @return JsonResponse
     */
    public function otp(AuthOtpRequest $request): JsonResponse
    {
        $result = $this->service->sendOtp($request->username);
        if (!$result['success']) {
            return response()->apiResult(null, 401, [__('messages.login_failed')]);

        }
        return response()->apiResult($result['data']);

    }
    /**
     * @OA\Post(
     *     path="/api/auth/validate",
     *     tags={"Auth"},
     *     summary="Validate OTP or password and get authentication token",
     *     description="Validates the provided OTP (for employees) or password (for other roles) and returns an auth token upon success.",     *     @OA\RequestBody(
     *         required=true,
     *         description="Validation credentials",
     *         @OA\JsonContent(ref="#/components/schemas/AuthValidateRequest")
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     * @param AuthValidateRequest $request
     * @return JsonResponse
     */
    public function validate(AuthValidateRequest $request): JsonResponse
    {
        $result = $this->service->Validate($request->username, $request->role, $request->pass);
        if (!$result['success']) {
            return response()->apiResult(null, 401, [__('messages.login_failed')]);

        }
        return response()->apiResult($result['data']);

    }
    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     summary="Log out the current user",     *     description="Invalidates the current user's access token.",
     *     security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->apiResult(messages: [__('messages.logout')]);

    }
}
