<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
	/**
	 * Display the password reset link request view.
	 *
	 * @return View
	 */
	public function create()
	{
		return view('auth.forgot-password');
	}

	/**
	 * Handle an incoming password reset link request.
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 *
	 */
	public function store(Request $request)
	{
		Validator::make(
			$request->only('email'),
			[
				'email' => [
					'required',
					'email',
					'exists:users,email'
				]
			],
			[
				'email.exists' => 'Пользователь с электронной почтой :input не существует. Введите электронную почту существующего пользователя'
			]
		)->validate();

		// We will send the password reset link to this user. Once we have attempted
		// to send the link, we will examine the response then see the message we
		// need to show to the user. Finally, we'll send out a proper response.
		$status = Password::sendResetLink(
			$request->only('email')
		);

		$email = $request->email;
		session()->put('success', "Письмо с информацией по сбросу пароля пользователя переслано на адрес электронной почты $email");

//		return $status == Password::RESET_LINK_SENT
//			? back()->with('status', __($status))
//			: back()->withInput($request->only('email'))
//				->withErrors(['email' => __($status)]);
		return redirect()->route('login');
	}
}
