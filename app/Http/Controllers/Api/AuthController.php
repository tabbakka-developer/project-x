<?php

namespace App\Http\Controllers\Api;

use App\Http\Helpers\APIResponseGenerator;
use App\Http\Requests\APICheckEmail;
use App\Http\Requests\APILoginRequest;
use App\Http\Requests\APIRegisterRequest;
use App\Http\Requests\PasswordRecoveryRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	public function login(APILoginRequest $request) {
		$credentials = $request->only('email', 'password');
		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				return APIResponseGenerator::responseFail([
					'text' => 'invalid_credentials',
					'code' => 401
				]);
			}
			/** @var User $user */
			$user = JWTAuth::toUser($token);
		} catch (JWTException $e) {
			return APIResponseGenerator::responseFail([
				'text' => 'could_not_create_token',
				'code' => 500
			]);
		}
		return APIResponseGenerator::responseTrue([
			'token' => $token,
			'user' => $user,
			'token_type' => 'bearer'
		]);
	}

	public function logout() {
		try {
			Auth::logout();
			return APIResponseGenerator::responseTrue(null);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function register(APIRegisterRequest $request) {
		$data = $request->all();
		try {
			$user = User::createUser($data);
			$credentials = [
				'email' => $data['email'],
				'password' => $data['password']
			];
			$token = JWTAuth::fromUser($user);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
		return APIResponseGenerator::responseTrue([
			'user' => $user,
			'token' => $token,
			'token_type' => 'bearer'
		]);
	}

	public function checkEmail(APICheckEmail $request) {
		$email = $request->only('email');
		$user = User::where('email', $email)->get();
		if ($user->count() > 0) {
			return APIResponseGenerator::responseTrue([
				'email' => 'not_empty'
			]);
		} else {
			return APIResponseGenerator::responseTrue([
				'email' => 'empty'
			]);
		}
	}

	public function passwordRecovery(PasswordRecoveryRequest $request) {
		$email = $request->input('email');
		$user = User::where('email', $email)->first();
		$password = str_random(10);
		$user->password = $password;
		$user->save();
		try {
			Mail::send('emails.forgot', array('password' => $password), function ($message) use ($user) {
				$message->to($user->email, $user->name)->subject('Forgot password');
			});
		} catch (\Exception $ex) {
			Log::error($ex);
			return APIResponseGenerator::responseFail([
				'text' => $ex->getMessage(),
				'code' => 500
			]);
		}
		return APIResponseGenerator::responseTrue(null);
	}
}
