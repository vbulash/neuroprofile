<?php

namespace App\Http\Controllers\neural;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NeuralController extends Controller
{
    public function shotDone(Request $request) {
		$png = base64_decode($request->photo);
		Storage::put('fake.png', $png);
		return response(status: 200, content: 'base64 decoded');
	}
}
