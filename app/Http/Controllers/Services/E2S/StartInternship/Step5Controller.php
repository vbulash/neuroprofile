<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\History;
use App\Models\Internship;
use App\Models\Student;
use App\Models\Timetable;
use App\Notifications\e2s\StartInternshipNotification;
use App\Support\PermissionUtils;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Exception;

class Step5Controller extends Controller
{
	//
	public function run()
	{
		$context = session('context');
		$view = 'services.e2s.start_internship.step5';

		return view($view, compact('context'));
	}

	// Создание
	public function create(Request $request) {
		$context = session('context');
		$history = History::create([
			'timetable_id' => $context['timetable']->getKey(),
			'student_id' => $context['student']->getKey(),
			'status' => 'Планируется'
		]);
		$history->save();

		$history->notify(new StartInternshipNotification($history));
		$id = $history->getKey();
		session()->forget('context');

		session()->put('success', "Стажировка № {$id} запланирована<br/>Письмо практиканту отправлено");
		return redirect()->route('history.show', ['history' => $id, 'sid' => session()->getId()]);
	}
}
