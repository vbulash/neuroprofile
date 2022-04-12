<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MainController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index()
	{
		return view('main');
	}
}
