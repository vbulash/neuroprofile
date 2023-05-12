<?php

namespace App\Http\Controllers\tests;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StepController extends Controller {
	public static array $steps = [
		StepCore::class,
		StepCard::class,
		StepMechanics::class,
		StepResults::class,
		StepPayment::class,
		StepTexts::class,
		StepBranding::class,
	];

	/**
	 * Проигрывание хода мастера теста
	 *
	 * @param int $mode
	 * @param int $test
	 * @return Application|Factory|View
	 */
	public function play(Request $request, int $mode, int $test) {
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();
		$buttons = 0;
		if (self::getCurrentStep() != 0)
			$buttons |= WizardButtons::BACK->value;
		if (self::getCurrentStep() == count(self::$steps) - 1) {
			if ($mode != config('global.show'))
				$buttons |= WizardButtons::FINISH->value;
		} else {
			$buttons |= WizardButtons::NEXT->value;
		}
		$request->merge([
			'mode' => $mode,
			'test' => $test,
			'buttons' => $buttons
		]);
		// Подготовка данных
		if ($mode != config('global.create') && !session()->has('heap')) {
			$test = Test::findOrFail($test);
			session()->forget('heap');
			$heap = [
				'name' => $test->name,
				'client' => $test->contract->client->getTitle(),
				'options' => $test->options,
				'paid' => $test->paid,
				'contract_id' => $test->contract_id,
				'key' => $test->key,
				'set_id' => $test->set_id,
				'cue' => $test->cue,
			];
			$content = json_decode($test->content, true);
			if (isset($content['card']))
				$heap['card'] = $content['card'];
			if (isset($content['descriptions']))
				$heap['descriptions'] = $content['descriptions'];
			if (isset($content['robokassa']))
				$heap['robokassa'] = $content['robokassa'];
			if (isset($content['branding']))
				$heap['branding'] = $content['branding'];
			if (isset($content['texts']))
				$heap['texts'] = $content['texts'];
			//
			session()->put('heap', $heap);
		}
		//session()->keep('heap');

		return match ($mode) {
			config('global.create') => $step->create($request),
			config('global.edit'),
			config('global.show') => $step->edit($request)
		};
	}

	/**
	 * Шаг назад
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function back(Request $request): RedirectResponse {
		if (self::getCurrentStep() > 0)
			self::decrementCurrentStep();

		$mode = $request->mode;
		$test = $request->test == 0 ? 0 : Test::findOrFail($request->test)->getKey();
		//		session()->keep('heap');
		return redirect()->route('steps.play', [
			'mode' => $mode,
			'test' => $test
		]);
	}

	/**
	 * Шаг вперед
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function next(Request $request): RedirectResponse {
		// При шаге вперед нужно сохранить результаты текущего шага
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();
		$mode = intval($request->mode);
		$test = ($request->has('test') && $request->test != 0) ? Test::findOrFail($request->test)->getKey() : 0;

		if ($mode != config('global.show'))
			Validator::make($request->all(),
				rules: $step->getStoreRules(),
				customAttributes: $step->getStoreAttributes()
			)->validate();

		$result = match ($mode) {
			config('global.create') => $step->store($request),
			config('global.edit') => $step->update($request),
			config('global.show') => true,
		};
		//		session()->keep('heap');
		if (!$result)
			return redirect()->route('steps.play', [
				'mode' => $mode,
				'test' => $test
			]);

		if (self::getCurrentStep() < count(self::$steps) - 1)
			self::incrementCurrentStep();

		return redirect()->route('steps.play', [
			'mode' => $mode,
			'test' => $test
		]);
	}

	/**
	 * Финальное сохранение / обновление записи теста
	 *
	 * @param Request $request
	 * @return RedirectResponse|null
	 */
	public function finish(Request $request) {
		// Сохранить информацию последнего шага
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();
		$mode = intval($request->mode);
		$test = intval($request->test);

		if ($mode != config('global.show'))
			Validator::make($request->all(),
				rules: $step->getStoreRules(),
				customAttributes: $step->getStoreAttributes()
			)->validate();

		$result = match ($mode) {
			config('global.create') => $step->store($request),
			config('global.edit') => $step->update($request),
			config('global.show') => true,
		};

		self::clearCurrentStep();

		if (!$result)
			return null;

		// Полное сохранение
		$heap = session('heap');
		$data = [
			'name' => $heap['name'],
			'options' => $heap['options'],
			'paid' => $heap['paid'],
			'contract_id' => $heap['contract_id'],
			'set_id' => $heap['set_id'],
			'cue' => $heap['cue'],
		];
		$content = [];
		if (isset($heap['card']))
			$content['card'] = $heap['card'];
		if (isset($heap['descriptions']))
			$content['descriptions'] = $heap['descriptions'];
		if (isset($heap['robokassa']))
			$content['robokassa'] = $heap['robokassa'];
		if (isset($heap['branding']))
			$content['branding'] = $heap['branding'];
		if (isset($heap['texts']))
			$content['texts'] = $heap['texts'];
		$data['content'] = json_encode($content);

		switch ($mode) {
			case config('global.create'):
				$data['key'] = Test::generateKey();
				$test = Test::create($data);
				$test->save();
				session()->put('success', "Тест \"{$test->name}\" создан");
				break;
			case config('global.edit'):
				$test = Test::findOrFail($test);
				$test->update($data);
				$test->save();
				session()->put('success', "Тест \"{$test->name}\" изменён");
				break;
			case config('global.show'):
				break;
		}

		session()->forget('heap');
		return redirect()->route('tests.index');
	}

	public function close(Request $request) {
		self::clearCurrentStep();
		session()->forget('heap');
		return redirect()->route('tests.index');
	}

	/**
	 * @return int
	 */
	public static function getCurrentStep(): int {
		$currentStep = session('step');
		if (!isset($currentStep)) {
			$currentStep = 0;
			session()->put('step', $currentStep);
		}
		return $currentStep;
	}

	/**
	 * @param int $currentStep
	 */
	public static function setCurrentStep(int $currentStep): void {
		session()->put('step', $currentStep);
	}

	public static function incrementCurrentStep(): int {
		return session()->increment('step');
	}

	public static function decrementCurrentStep(): int {
		return session()->decrement('step');
	}

	public static function clearCurrentStep(): void {
		session()->forget('step');
	}

	protected static function getCurrentStepClass(): string {
		return self::$steps[self::getCurrentStep()];
	}
}