<?php

namespace App\Http\Controllers;

use App\Http\Controllers\results\BlocksArea;
use App\Http\Controllers\results\BlocksComposer;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MainController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index()
	{
		return view('empty');
		// TODO Убрать заглушку, сделать нормальный dashboard
//		return view('main');
	}
}
