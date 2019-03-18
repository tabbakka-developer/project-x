<?php

namespace App\Http\Controllers\Api;

use App\Http\Helpers\APIResponseGenerator;
use App\Http\Requests\EditUserRequest;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller {

	public function get() {
		try {
			$user = Auth::user();
			if (!$user) {
				return APIResponseGenerator::responseFail([
					'text' => 'User not found',
					'code' => 404
				]);
			}
			return APIResponseGenerator::responseTrue([
				'user' => $user
			]);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function edit(EditUserRequest $request) {
		$editable = $request->except('token');
		/** @var $user User */
		$user = Auth::user();
		if (!$user) {
			return APIResponseGenerator::responseFail([
				'text' => 'User not found',
				'code' => 404
			]);
		}
		try {
			if ($editable['password'] != null) {
				if (!$user->checkPassword($editable['old_password'])) {
					return APIResponseGenerator::responseFail([
						'text' => 'Password mismatch',
						'code' => 403
					]);
				}
			} else {
				unset($editable['password']);
				unset($editable['old_password']);
				unset($editable['password_confirmation']);
			}
			$user->update($editable);
			return APIResponseGenerator::responseTrue($user);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function paymentHistory() {

	}

	public function list() {
		try {
			$users = User::whereHas('roles', function ($q) {
				$q->where('role_name', 'user');
			})->get();

			return APIResponseGenerator::responseTrue([
				'users' => $users
			]);
		}
		catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function show($user_id) {
		try {
			$user = User::with('events')->where('id', $user_id)->get();
			return APIResponseGenerator::responseTrue([
				'user' => $user
			]);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function block($user_id) {
		/** @var User $user */
		$user = User::find($user_id);
		if ($user != null) {
			if ($user->block()) {
				return APIResponseGenerator::responseTrue([
					'user' => $user
				]);
			} else {
				return APIResponseGenerator::responseFail([
					'code' => 500,
					'text' => 'Error blocking user'
				]);
			}
		} else {
			return APIResponseGenerator::responseFail([
				'code' => 404,
				'text' => 'User not found'
			]);
		}
	}

	public function unblock($user_id) {
		/** @var User $user */
		$user = User::find($user_id);
		if ($user != null) {
			if($user->unblock()) {
				return APIResponseGenerator::responseTrue([
					'user' => $user
				]);
			} else {
				return APIResponseGenerator::responseFail([
					'code' => 500,
					'text' => 'Error unblocking user'
				]);
			}
		} else {
			return APIResponseGenerator::responseFail([
				'code' => 404,
				'text' => 'User not found'
			]);
		}
	}
}
