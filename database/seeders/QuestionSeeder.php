<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Set;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$script = <<<EOS

EOS;

		$set = Set::create([
			'name' => 'Артигас-3',
			'code' => <<<'EOS'
<pre><code class="language-php">// Код имеет доступ к ...
// Код должен вернуть код вычисленного нейропрофиля

$data = array_count_values($result);
ksort($data);
foreach (["A+", "A-", "B+", "B-", "C+", "C-", "D+", "D-", "E+", "E-"] as $letter)
	if (!isset($data[$letter]))
		$data[$letter] = 0;

if($data["A+"] &gt;= $data["A-"]) {
    if($data["B+"] &gt;= $data["B-"]) {
        if($data["C+"] &gt;= $data["C-"]) {
            if($data["D+"] &gt;= $data["D-"]) {
                return "PR";
            } else {
                return "PA";
            }
        } else {
            if($data["D+"] &gt;= $data["D-"]) {
                return "OI";
            } else {
                return "OA";
            }
        }
    } else {
        if($data["C+"] &gt;= $data["C-"]) {
            if($data["D+"] &gt;= $data["D-"]) {
                return "BP";
            } else {
                return "BH";
            }
        } else {
            if($data["D+"] &gt;= $data["D-"]) {
                return "CS";
            } else {
                return "CV";
            }
        }
    }
} else {
    if($data["B+"] &gt;= $data["B-"]) {
        if($data["C+"] &gt;= $data["C-"]) {
            if($data["D+"] &gt;= $data["D-"]) {
                return "PK";
            } else {
                return "PP";
            }
        } else {
            if($data["D+"] &gt;= $data["D-"]) {
                return "OV";
            } else {
                return "OO";
            }
        }
    } else {
        if($data["C+"] &gt;= $data["C-"]) {
            if($data["D+"] &gt;= $data["D-"]) {
                return "BO";
            } else {
                return "BD";
            }
        } else {
            if($data["D+"] &gt;= $data["D-"]) {
                return "CE";
            } else {
                return "CO";
            }
        }
    }
}
//</code></pre>
EOS
		]);
		$set->save();

		$questions = [
			[
				'sort_no' => 1,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/Iacd5M38dbWYX1PDvBDYXMqUzAnZqJ7wVuuhucrM.png',
				'image2' => 'images/2021-06-15/OcoAgllPzAqEEJGVXyEBhdQLl3WxxgmHEjAXUXLn.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 2,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/GbxJqgKNOvXwhohS9B9ppY7JHK1Y452iARmmEOeX.png',
				'image2' => 'images/2021-06-15/H0cteqajr4B8jmJh15bFXS5FofYCFj8fghnLIxL1.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 3,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/q1i0DB4nI4ywM2ob0jncetCnk8LlnEVK7S5jik1L.png',
				'image2' => 'images/2021-07-05/ohgRuENj8g5kArecOVI5MuxfMTK8mLGkoRyNFi4G.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 4,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/Tyy4n9zxnHh1jMASfS0bcO40GYBiJGFK3Iacqr3j.png',
				'image2' => 'images/2021-06-15/hY6TORtamOyR0p5i1LMNtO8ufZXmW6sFqq7cw2rN.png',
				'value1' => 'D+',
				'value2' => 'D-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 6,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/t5dOXND0HAk1BQTj2C1J3IexlvpZD9gjciLCCdrz.png',
				'image2' => 'images/2021-06-15/98uvXwV1IHgVyZyxp66WhX1LGuGYTIqqB1Twwqxb.png',
				'value1' => 'C+',
				'value2' => 'C-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 7,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/xVuAdRchSPRUWl3XN6aWmvBnknUcN9h5mJOzr4Jo.png',
				'image2' => 'images/2021-06-15/xYRfCUUMxSYcdnEStmHP0ZEzBSIG8S5PJR9lKgjI.png',
				'value1' => 'A+',
				'value2' => 'A-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 10,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/sRlfdWkXVGlYt9zKZUOB2e4FZXUBhwrl7LlQVTi4.png',
				'image2' => 'images/2021-06-15/o82pUulhmSUYXfTOoA6X9XgT9qG04eafPmu6Xu3P.png',
				'value1' => 'B-',
				'value2' => 'B+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 11,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/aC95WeD0RR2u8Wgw7591TqK8Y98KLc6g5kpfzQGG.png',
				'image2' => 'images/2021-06-15/0YhfbAVS2OUMKwPGAZ1acKXye1K8BTFg8yqbXwns.png',
				'value1' => 'A-',
				'value2' => 'A+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 13,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/yzXcFeDmbK8VDvToeXyB9H3Ea22t3AfcRZjAKmTA.png',
				'image2' => 'images/2021-06-15/xR50fSSs7qqGgjJTEQOeRWpxBd5NESe4d7nlJvR0.png',
				'value1' => 'D-',
				'value2' => 'D+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 15,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/5zKicvl3KbRQWPovRf7Nf1o1AieoMEwfql1PuPAo.png',
				'image2' => 'images/2021-06-15/Qqme4KvSi9rGvg5WwIDh5IlN993pQWCsZyitFp7X.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 16,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/VWPuFJGKFQXIm40DoFTHCaPQ1mc5ZjHBLhxLHyEN.png',
				'image2' => 'images/2021-06-15/S9X7JVNrknm3X8vGYCmwnhDW5EqlwuGJnmTgHHp3.png',
				'value1' => 'B-',
				'value2' => 'B+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 18,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/qtvKBzvXPdo0ttNJNebirt4dvK5njnravzrWEB3T.png',
				'image2' => 'images/2021-06-15/MY7viYPUGLzGwiXvmc0BbDFUa7hLdefrtQlOPAsr.png',
				'value1' => 'C+',
				'value2' => 'C-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 19,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/1600eISYBku3qTS23kQLC3iQMz9pZR3wwIfuPLt1.png',
				'image2' => 'images/2021-06-15/BCGsCVZQlHTxdQuQGU82FzP9cRuUHObxYPmfgLYC.png',
				'value1' => 'B-',
				'value2' => 'B+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 20,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/isApEeCGI8INycvh0KF5DCEjz2tITnGpkX5VW311.png',
				'image2' => 'images/2021-06-15/a3ZnfBKeMC8FomlztWbnrk4BcY9r77s4yBSrABeQ.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 21,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/yXnnVB5ODHUas3lwFkMBmyHf07HkxsgomgYtPfiZ.png',
				'image2' => 'images/2021-06-15/ABuitmCwAF9mmIRQpvVcS43H4td2B0Q36zmW4CiF.png',
				'value1' => 'A+',
				'value2' => 'A-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 25,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/bPlLeXLIX9lbuVmY9ZYCSym6cvy1qxiE1FXGBK4r.png',
				'image2' => 'images/2021-06-15/9fiLU50oGTynvAVFMuhmjkxgnBO2fEyMMQbjSrek.png',
				'value1' => 'D+',
				'value2' => 'D-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 26,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/mDyTeqyGXPhNKmq7qn1Xk3xMEy1CMRj4s0NnrsLm.png',
				'image2' => 'images/2021-06-15/4vq1Zla2ushBGs0F9IfeYBkjk6YWyn2OxGH0ZxG3.png',
				'value1' => 'D+',
				'value2' => 'D-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => $set->getKey(),
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/zgNNU1BZ3ooPwmuTYQmDP89pCpJ3KnCid4CXbhGF.png',
				'image2' => 'images/2021-06-15/9M3jIetU2zlRneJWPOBvk3E02ylBrELNHKTMYjUn.png',
				'value1' => 'D+',
				'value2' => 'D-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => $set->getKey(),
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/K2BsloQKfdxyWpmLjnUP8lHJhZAh6dzNa5bW0DWa.png',
				'image2' => 'images/2021-06-15/1iNqW2HMg1xfvSLDaUKUKPfw7hveECCs9TLxYqd8.png',
				'value1' => 'C-',
				'value2' => 'C+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 31,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/tdzQ6eJzQpYDF5Pkp5ro8sHnPFnK48FDQGKPPXui.png',
				'image2' => 'images/2021-06-15/NhsbNFVKa89284HsBRhAdzPtEUl36FLTaAybmiLs.png',
				'value1' => 'A+',
				'value2' => 'A-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 31,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-07-05/SFngwLpyq60UTa6qkEmsAG3ZaXA1Q5usv8XSpJ44.png',
				'image2' => 'images/2021-07-05/TZry6HAghbxfjBz7HQFrCnGTOiYwXkcVUtUa8leM.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 32,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/C05jsJMFVH7FGvShWnqf9EwzgCurNfGhkMqbYMol.png',
				'image2' => 'images/2021-06-15/PQwDCSemNhCZNlWXkndWuYMQVXPKVZSlV10e5Gf1.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 32,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-07-05/WRtCcML28n4XJviJh2tomzGtfFHEbKnlbTdkkZWV.png',
				'image2' => 'images/2021-07-05/CCfcBN4wBlBglabzmm55hfLzr9UgnjKnvSRkoPzC.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 33,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-07-05/TRYtOO2EaXTrqXtorPlTXyyh2kxwXvNziCPsRNzj.png',
				'image2' => 'images/2021-07-05/RW815XwdvRQbU5yvx5E2YNcf4wTqnPYSGAetrehF.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 34,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/jEpXuPMsvfTV4quSLemrujirOvxY0LMc8gwR6hGh.png',
				'image2' => 'images/2021-06-15/zwthJBsAnH8rK3g5rNTTYnH2NMkNkJAj3txUrcls.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 34,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-07-05/SLKeACmWWKNyp02QoeYMQR9i6Wc3lzYGB7XbEPeF.png',
				'image2' => 'images/2021-07-05/8os0oEwiGmHbLoFQjOW2PZCIz2b75e1UknrHseF6.png',
				'value1' => 'B-',
				'value2' => 'B+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 35,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/P1hHBavd6asNAOhYpk8zzusVpXd4kioGu9foOwKY.png',
				'image2' => 'images/2021-06-15/uj2u56BrtXDSPmDuyuRruUoXjFTq6bdwAyDvoOon.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 35,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-07-05/qRVT9LMn0ddLvpgiqBA6RixZF0HozRdIqHX3sHi2.png',
				'image2' => 'images/2021-07-05/diymp6QUJYpvDU2dvDTAabf7aBhSF9s75u3XRE35.png',
				'value1' => 'B+',
				'value2' => 'B-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 36,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-07-05/2cA2dz9pOtUsy832EMvJgGhY5s1vydXvBinpk0Vu.png',
				'image2' => 'images/2021-07-05/hnAJBqZ0GHdCaf5xUkBHw1TbgMNn5H6r7HXMQPHK.png',
				'value1' => 'A-',
				'value2' => 'A+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 37,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-07-05/Hf1BrBRLuTuw2ApXMn6yhZsG3Ln7nuYzg70v7Y30.png',
				'image2' => 'images/2021-07-05/0p830ilKsKmGk8ADy7VbwF4A60hm94VbLNGnVcWn.png',
				'value1' => 'C+',
				'value2' => 'C-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 39,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/sdqy7mZMSUMiY9Gkimfp15JcHUKAdKNt23G9RafM.png',
				'image2' => 'images/2021-06-15/9X0RY40D8A3ixD4Gg1DubNGZoMVJPvxMiuBZ2bKu.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 40,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/JeFfGcIT13gmEdZ4nFxu5Ww1X8urhywLHqdmLo5j.png',
				'image2' => 'images/2021-06-15/ndae2F9oBkHHVUZIOoQqJf3rYlcYBtjPxtn6xG7X.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 44,
				'learning' => 0,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/lBKgRHTZoNVGpmzT2Ex0N8Ymp0Aj1KMJQQjTTv5x.png',
				'image2' => 'images/2021-06-15/EmbUN4tkuTIJXaYHeM3iNruATKkmWxzZ76fFILQ2.png',
				'value1' => 'C-',
				'value2' => 'C+',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 47,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/0Cp0tyfh4B06SY6kNxM2wkdbOlv2cljhmGBEVdVR.png',
				'image2' => 'images/2021-07-05/t3R1dEbNGCsMJZFchwVTNE36TbrhoMy1YVAYuq7q.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 49,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/fCBq95rHoarqy21sguobaFhB8gWwtTNjhafIi0XF.png',
				'image2' => 'images/2021-06-15/UVyKPhtLJCOG8y8EA5lCDUij01V2D2DyXSPGogm4.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			],
			[
				'sort_no' => 51,
				'learning' => 1,
				'timeout' => 5,
				'image1' => 'images/2021-06-15/OzGjN844eHildroAVgsqOfasm8fCRoUztyXL4fLb.png',
				'image2' => 'images/2021-06-15/e3YTG8yxhPBbgo9IVwzK4X0n8LSHGpopHRJT6p0P.png',
				'value1' => 'E+',
				'value2' => 'E-',
				'set_id' => $set->getKey()
			]
		];
		$records = collect($questions)->map(function ($record) {
			return [
				'sort_no' => $record['sort_no'],
				'learning' => $record['learning'],
				'timeout' => $record['timeout'],
				'image1' => $record['image1'],
				'image2' => $record['image2'],
				'value1' => $record['value1'],
				'value2' => $record['value2'],
				'set_id' => $record['set_id'],
				'created_at' => now(),
				'updated_at' => now()
			];
		});
		Question::insert($records->toArray());

		$set = Set::create([
			'name' => 'Бельграно-1',
			'code' => <<<'EOS'
// Код должен вернуть код вычисленного нейропрофиля

$keys = ["НК-O", "НК-P", "НК-C", "НК-B", "НГ-S", "НГ-T", "НГ-F", "НГ-N", "П-1", "П-2", "П-3", "П-4"];
$channel = array_fill_keys($keys, 0);

$index = 0;
foreach ($result as $question) {
    switch($index++) {
        case 3:
            if($question == "A+") {
                $channel["НК-P"]++;
                $channel["НК-C"]++;
                $channel["НГ-T"]++;
                $channel["НГ-F"]++;
                $channel["П-2"]++;
            } elseif ($question == "B+") {
                $channel["НГ-S"]++;
                $channel["НГ-N"]++;
                $channel["П-1"]++;
            }
            break;
        case 7:
            if($question == "A+") {
                $channel["НК-O"]++;
                $channel["НГ-S"]++;
                $channel["П-1"]++;
                $channel["П-2"]++;
                $channel["П-4"]++;
            }
            break;
        case 10:
            if ($question == "A+") {
                $channel["НК-O"]++;
                $channel["НК-P"]++;
                $channel["НГ-S"]++;
                $channel["НГ-F"]++;
                $channel["НГ-N"]++;
                $channel["П-1"]++;
                $channel["П-3"]++;
            }
            break;
        case 13:
            if ($question == "A+") {
                $channel["НК-B"]++;
                $channel["НГ-S"]++;
                $channel["П-2"]++;
                $channel["П-4"]++;
            } elseif ($question == "B+") {
                $channel["НГ-F"]++;
            }
            break;
        case 16:
            if ($question == "A+") {
                $channel["НК-P"]++;
                $channel["НГ-N"]++;
            } elseif ($question == "B+") {
                $channel["НК-O"]++;
                $channel["НК-B"]++;
                $channel["НГ-S"]++;
                $channel["НГ-T"]++;
                $channel["П-2"]++;
                $channel["П-4"]++;
            }
            break;
        case 19:
            if ($question == "B+") {
                $channel["НК-P"]++;
                $channel["НГ-S"]++;
                $channel["П-2"]++;
            }
            break;
        case 22:
            if ($question == "A+") {
                $channel["П-3"]++;
            } elseif ($question == "B+") {
                $channel["НК-P"]++;
                $channel["НК-B"]++;
                $channel["НГ-T"]++;
                $channel["П-2"]++;
                $channel["П-4"]++;
            }
            break;
        case 25:
            if ($question == "A+") {
                $channel["НК-P"]++;
                $channel["НГ-S"]++;
                $channel["П-2"]++;
            } elseif ($question == "B+") {
                $channel["НК-C"]++;
                $channel["П-3"]++;
            }
            break;
        case 28:
            if ($question == "A+") {
                $channel["НК-P"]++;
                $channel["НК-C"]++;
                $channel["НК-B"]++;
                $channel["НГ-S"]++;
                $channel["П-2"]++;
            }
            break;
        case 31:
            if ($question == "A+") {
                $channel["НК-P"]++;
                $channel["НГ-S"]++;
                $channel["НГ-N"]++;
                $channel["П-2"]++;
            }
            break;
        case 34:
            if ($question == "A+") {
                $channel["НК-B"]++;
            } elseif ($question == "B+") {
                $channel["НК-O"]++;
                $channel["НК-P"]++;
                $channel["НГ-F"]++;
                $channel["НГ-N"]++;
                $channel["П-1"]++;
                $channel["П-2"]++;
            }
            break;
        case 38:
            if ($question == "A+") {
                $channel["НК-P"]++;
                $channel["НГ-F"]++;
                $channel["НГ-N"]++;
                $channel["П-3"]++;
            } elseif ($question == "B+") {
                $channel["П-4"]++;
            }
            break;
        case 41:
            if ($question == "B+") {
                $channel["НК-O"]++;
                $channel["НК-C"]++;
                $channel["НГ-S"]++;
                $channel["НГ-F"]++;
                $channel["П-1"]++;
                $channel["П-2"]++;
            }
            break;
        case 44:
            if ($question == "B+") {
                $channel["НК-C"]++;
                $channel["НК-B"]++;
                $channel["НГ-S"]++;
                $channel["П-1"]++;
                $channel["П-4"]++;
            }
            break;
        case 47:
            if ($question == "A+") {
                $channel["НК-O"]++;
                $channel["П-1"]++;
            } elseif ($question == "B+") {
                $channel["НК-P"]++;
                $channel["НК-C"]++;
                $channel["П-4"]++;
            }
            break;
        case 50:
            if ($question == "A+") {
                $channel["НК-O"]++;
                $channel["НК-P"]++;
            } elseif ($question == "B+") {
                $channel["НК-B"]++;
            }
            break;
        case 53:
            if ($question == "A+") {
                $channel["НК-P"]++;
                $channel["НГ-N"]++;
            } elseif ($question == "B+") {
                $channel["НК-O"]++;
            }
            break;
        default:
            break;
    }
}

$maxweight = [
    "НК-O" => 8,
    "НК-P" => 13,
    "НК-C" => 6,
    "НК-B" => 7,
    "НГ-S" => 11,
    "НГ-T" => 3,
    "НГ-F" => 6,
    "НГ-N" => 7,
    "П-1" => 7,
    "П-2" => 11,
    "П-3" => 4,
    "П-4" => 7
];
$weight = [];
foreach ($keys as $key) {
    $weight[$key] = $channel[$key] /  $maxweight[$key];
}

$nk = $ng = $p = [];
foreach ($weight as $key => $value) {
    if (str_contains($key, "НК-")) $nk[$key] = $value;
    elseif (str_contains($key, "НГ-")) $ng[$key] = $value;
    elseif (str_contains($key, "П-")) $p[$key] = $value;
};

$pair = [];
$pairIndex = 0;
$mins = array_keys($nk, min($nk));
if (count($mins) == 1) $pair[$pairIndex++] = $mins[0];
$mins = array_keys($ng, min($ng));
if (count($mins) == 1) $pair[$pairIndex++] = $mins[0];
if ($pairIndex < 2) {
    $mins = array_keys($p, min($p));
    if (count($mins) == 1) $pair[$pairIndex] = $mins[0];
}
$map = [
    "НК-O" . "/" . "НГ-S" => "OI",
    "НК-O" . "/" . "П-1" => "OI",
    "НГ-S" . "/" . "П-1" => "OI",
    "НК-O" . "/" . "НГ-T" => "OA",
    "НК-O" . "/" . "П-2" => "OA",
    "НГ-T" . "/" . "П-2" => "OA",
    "НК-O" . "/" . "НГ-F" => "OO",
    "НК-O" . "/" . "П-3" => "OO",
    "НГ-F" . "/" . "П-3" => "OO",
    "НК-O" . "/" . "НГ-N" => "OV",
    "НК-O" . "/" . "П-4" => "OV",
    "НГ-N" . "/" . "П-4" => "OV",
    "НК-P" . "/" . "НГ-T" => "PA",
    "НК-P" . "/" . "П-1" => "PA",
    "НГ-T" . "/" . "П-1" => "PA",
    "НК-P" . "/" . "НГ-S" => "PR",
    "НК-P" . "/" . "П-2" => "PR",
    "НГ-S" . "/" . "П-2" => "PR",
    "НК-P" . "/" . "НГ-F" => "PP",
    "НК-P" . "/" . "П-4" => "PP",
    "НГ-F" . "/" . "П-4" => "PP",
    "НК-P" . "/" . "НГ-N" => "PK",
    "НК-P" . "/" . "П-3" => "PK",
    "НГ-N" . "/" . "П-3" => "PK",
    "НК-C" . "/" . "НГ-S" => "CS",
    "НК-C" . "/" . "П-4" => "CS",
    "НГ-S" . "/" . "П-4" => "CS",
    "НК-C" . "/" . "НГ-T" => "CV",
    "НК-C" . "/" . "П-3" => "CV",
    "НГ-T" . "/" . "П-3" => "CV",
    "НК-C" . "/" . "НГ-F" => "CO",
    "НК-C" . "/" . "П-2" => "CO",
    "НГ-F" . "/" . "П-2" => "CO",
    "НК-C" . "/" . "НГ-N" => "CE",
    "НК-C" . "/" . "П-1" => "CE",
    "НГ-N" . "/" . "П-1" => "CE",
    "НК-B" . "/" . "НГ-T" => "BH",
    "НК-B" . "/" . "П-4" => "BH",
    "НГ-T" . "/" . "П-4" => "BH",
    "НК-B" . "/" . "НГ-S" => "BP",
    "НК-B" . "/" . "П-3" => "BP",
    "НГ-S" . "/" . "П-3" => "BP",
    "НК-B" . "/" . "НГ-F" => "BD",
    "НК-B" . "/" . "П-1" => "BD",
    "НГ-F" . "/" . "П-1" => "BD",
    "НК-B" . "/" . "НГ-N" => "BO",
    "НК-B" . "/" . "П-2" => "BO",
    "НГ-N" . "/" . "П-2" => "BO"
];

foreach ($map as $key => $value)
	if ($key === $pair[0] . "/" . $pair[1])
		return $value;
//
EOS
		]);
		$set->save();

		$questions = [
			[
				"sort_no" => 1,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/AdAx2EIMW8lbygLWRpve3AUKvdXHRZ1dc2Bcb2fE.png",
				"image2" => "images/2022-01-17/KHGVoE08UoJmA924UlSL53pMWqWNnjluAidJnFBi.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 2,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/3K37JcLBpjyDTxYOIWefEMEp0iOyyToMeNLAO3M4.png",
				"image2" => "images/2022-01-17/Cy91cBU5nhDqXv2FLG55VjHDXWuPCP3r996mo5f0.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 3,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/XIgnqhvDLQmjMFtnrdAKSU0ccXnRsF7F84YUh7WP.png",
				"image2" => "images/2022-01-17/oPm4jhXuzybuFhdlA13wmkRGjel2GOFnJwosHd57.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 4,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-12/Ah4fZ4Gp45GY8LDjViUqzgpw21ZSXDuq9MGFZAsS.png",
				"image2" => "images/2022-01-12/qpMUZ9v2Bwze50FieCOH3w5WV2QG9IJ0s05SRAqN.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 5,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/0VRRVLiNvPp6zXRruk8lfdLPgOuBCxXvYwkJejWQ.png",
				"image2" => "images/2022-01-17/0GTGZExcvOIKu6dZBNJCFfBih17XrMPex9sKm1Kb.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 6,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/XErebUDjzk4pxihq978011eE4oSFkBSViMgAG0L1.png",
				"image2" => "images/2022-01-17/V3cXqiuhFfka4sn8DUod3PCzEoI7jtMlmea0Q7Cm.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 7,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/lyD5Wjd5bl4s5wGQWfMVgOF9fo2HxQNH76rE89Oo.png",
				"image2" => "images/2022-01-17/Swzd3y6LdLWu1dnrrSQ5J4wVKDcKD9XQtLtadO5D.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 8,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/LcyEZgGUf6ZDySRfqAHLfLxXuo441G740mVuhmjc.png",
				"image2" => "images/2022-01-17/3A6N9snFhHNZonju86MQjvAex5cRqNnUFDtFd2mT.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 9,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/LBuguWJseisg0vevLUwVLAtZcqDifKyUWREeXJgA.png",
				"image2" => "images/2022-01-17/bq0GRmFAib5xGpBuFeOJPZwWUdp1Dd9EruwYgqSf.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 10,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/2tbkvV7jARFRnDLYlAN7TaWvNQPpWwrr5m2VIraG.png",
				"image2" => "images/2022-01-17/btyrRm1bKcIo3fLfKwCXt2jzmWkP3rtDE0vFBjoK.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 11,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/zeCUHnm7R4vAMX8B8hcR6WqhosN25YONbuHTDkRc.png",
				"image2" => "images/2022-01-17/FaMCVd49NBDEBERbzVPuNtNvjlCgbmVb7QP8cp4S.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 12,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/y0kb8isna8IMharajlHBZupdrwbN5wiFUM9kTlzi.png",
				"image2" => "images/2022-01-17/6LOleAEvRrTYLt6OFxl8MvoqT4nhscToOls0qXx5.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 13,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/isB9HXO3wDrz6AFloxM1MFDxhOqVuqfGV9DunzCr.png",
				"image2" => "images/2022-01-17/s3hvhp8gBqHW7UYeVPOdn863tgkJvedr0R8fp7nz.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 14,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/pjQsvYBC9qfcijnnGsncsSLmjAidL6NVbOsdjaW5.png",
				"image2" => "images/2022-01-17/KztVco9IqWLK5quxYDUvYhs9B5mKmIMPF2GMPzUt.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 15,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/8hrf5meBMPwNyiyIpPsYEtWuhVMUTAdSTbQMGON4.png",
				"image2" => "images/2022-01-17/uE5Z8K6CCjnwDRHLiTzue4HeOcLaGJhqh5Jkj33B.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 16,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/IUdcfpSEK8kQ9vn51FnrmCnvtcrZKDBjZ7bmpNNx.png",
				"image2" => "images/2022-01-17/rUseft1NfpkfxLFXCmdr8wLW78b2jGv7BymEl9Ue.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 17,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/c72E3aRGChy8Q2JrUHfR4IekGPXKyFD6x3p321eq.png",
				"image2" => "images/2022-01-17/SBTvneedglRsq057fOvJjGVJfQ8hCK86ZXRKKnct.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 18,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/3Euw1uuRBTfBHDYTfNcOlM70XhbtikfsSczsMbyc.png",
				"image2" => "images/2022-01-17/hB2saBlXExNOK3g0qrZ6axgBLRh4YzOw3K9rvyUm.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 19,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/ounqwIU9XmnVYtWRoxzBf4CeSF7Wf60X3TB3mxBi.png",
				"image2" => "images/2022-01-17/zKB7BuJZdLJuuOSvJzWR66IQqqstCPIrKLqk5EOU.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 20,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/0yUpTBnV24kVoVnq1H5G6GTZ8QYkNHELONpp91XD.png",
				"image2" => "images/2022-01-17/O8dvpU48gn5mNDFlMfa6K353hdSzB40Pc99u6UOk.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 21,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/gK8qylPOoGIrIfpttfzPVUNWg5GLQZ68A43UwtEW.png",
				"image2" => "images/2022-01-17/InOcH2d71RiBwi4R7Wv3xpH3LwSf2wRH8S3e7UtZ.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 22,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/6QZttbhdkjbc1prkuBOchGG56i9OdkNhJHwEBIQC.png",
				"image2" => "images/2022-01-17/TXLVwF4ec3Rnko37cZ1NeKNtvEtX4KuVu9EaGxmZ.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 23,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/5aGPfRHMMnlmgNmQ2xAMZta6cZQRphA2a6FGVAac.png",
				"image2" => "images/2022-01-17/YidvjX5iCzAjNgE3R1hWSPM3L1njcH33L5ySz70h.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 24,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/qkaQihBWGN5y6LSdLrVtUQeVwjiCDUgK9DiiVGe0.png",
				"image2" => "images/2022-01-17/afGsXcIXwoHdyqBg8rcjVsvR8TxUo9D8mjubUPNe.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 25,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/SmaiDAdhJumRXZQWaeHNOgklOvkZwN0xosujBo8e.png",
				"image2" => "images/2022-01-17/cd5fLPm406Tyk52wN76YfHkkEPqlme9RJNkBXSR1.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 26,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/FzN0pKc5mcsr9dBH0e8ug3dg4ejvRSqqclFEXPlX.png",
				"image2" => "images/2022-01-17/C0OXP0ZnTRKOG7kicXSZdiF3BQkRy86YJTQxgWQ0.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => $set->getKey(),
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/tLN71QAcTzAKXoVcTW0Qiwb7CCnqravnmw8nMQUS.png",
				"image2" => "images/2022-01-17/eyOnMFQ3N8Eu8e48IUQU6yp231rEnymI9iJUUbEk.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 28,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/f6fanXC8HTtQH7qKZQBMsb7HuJG0tIMPNRtYNLZN.png",
				"image2" => "images/2022-01-17/4A2m43xdfGDMLWM1rI6qWONoc04bPPairo7CZrZk.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => $set->getKey(),
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/hrVCuqidz6yq8E5yBKbvN7sLbSpK8T5aufCbJxk6.png",
				"image2" => "images/2022-01-17/Ps5L3mzltW2I316UuA1SINrfZz06PrfSYegqx1du.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 30,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/IUjrslh54Z2z7W2QRhikCJKYd9g5btPh8nQiSmri.png",
				"image2" => "images/2022-01-17/Y62yGWRkTYp8nKsq3t6RkejhM6eLG4aRvJ3zQg7W.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 31,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/Iyna4BDI5htYX4hY9hO8KPLlZix7yTmIfJmICThj.png",
				"image2" => "images/2022-01-17/Q7YPvamlgotHmYVpHUw7scmpvctdatxrWrseRgNT.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 32,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/8JE0cmpcqhwTQB5YsNRcjOLAny3zesvb2SWgurww.png",
				"image2" => "images/2022-01-17/TFpjncZs7RYGktY04YTaNvYzZnwDzAUvz1btRb71.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 33,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/LSONQ3upVq1ao7zaNowNor4X4vAoYWXh9UC11SJu.png",
				"image2" => "images/2022-01-17/QjlUeZtwmATYyvCn5LAkkFHN0Q4fx6kuukTSj1Wt.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 34,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/kUQbSQJS4FN1CSOVbm0e4P9RBdYPawCCVcS6csjT.png",
				"image2" => "images/2022-01-17/PLtbcsfBOfR98Cw70fXQmiKy7sY0p5DLsuIIdxBV.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 35,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-17/06U1c6BVjwQdTNN8QIjhfcW9J3pUcGsD5fhXsqTC.png",
				"image2" => "images/2022-01-17/RdrMQKNAg1kgtLdVkjsTvpfTYG7hSqEZhLb6zh5z.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 36,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/3ccxf3RnvST4d9HOba2xOC5TujHUrbhb2SQDmatw.png",
				"image2" => "images/2022-01-17/YbXMPlQyKQ1H3VK7sA3kBqE5m8mbIDFUGGfhfJwz.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 37,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/nqrXtVnABmOsfBQ23yQxEX2yaMG0AgcyTJxx7IOw.png",
				"image2" => "images/2022-01-17/4iiioKUU5JuQiezeU1EXeEKgU2DWbkVMPbvDzVJD.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 38,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-17/KfV933fIbUQ806tVLUmmGYHCEKxEmVbrNtt5tIiH.png",
				"image2" => "images/2022-01-17/5nzZ3mdRdDtv7Au1QzL6pGwjoD1Iz2xkXlY07YDC.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 39,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-18/CkiJJ9NAzFG5uQsfBBQSur2kWpTf5CU2CB6LmbYq.png",
				"image2" => "images/2022-01-18/BmKbsi0h2XqhgikYMni3l7EKWrtVmZzirbUlJIck.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 40,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/jLTsFeNJJuOjQXBLveh1kRgNIqWVStwpgmChI55a.png",
				"image2" => "images/2022-01-18/stqxy6OjToSK0ssUK3K6zIeoVvbDYMsKGUw56H3o.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 41,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/kwM675f2hYTpth1Q91XhC79QsEpMj9x3QHkyuH9q.png",
				"image2" => "images/2022-01-18/aPKgZmykooZt4M6a7eZGRxlpkO2ofPqhtXsfFaA7.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 42,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-18/aMCOGj9js04ovIwYBdKTdxhDJFI0900pQveLTASI.png",
				"image2" => "images/2022-01-18/xRNziOFeqad38IuFTROP3pXgkVulHAiBn2t2gJ3F.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 43,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/Grhqef5fjVyqLKz3xGjFehXDDRysNV1SlVV6WnFz.png",
				"image2" => "images/2022-01-18/PA95rnu6vRrgaIV05zYeSgeUtIFgWtXdkM1usLtO.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 44,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/tzxrKUdsu7WChky36e8l5QEzmh0XyXUswlHdbN7l.png",
				"image2" => "images/2022-01-18/bKLIW0eBQPwAS1R49vV6oxMloychu4YohRr8pFyx.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 45,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-18/82tvD2PhpN1Dco5Dj6gj1ya00VdIH1a4CCSxWD78.png",
				"image2" => "images/2022-01-18/E5ENapcfJ7jP080o9AiUQ27tYYXgtKvqKNVhjxvf.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 46,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/ggGxvfnTAzizwGcCe2UHorMlsVmNBFWGdSMZ4c7I.png",
				"image2" => "images/2022-01-18/BauZZxJOBLSR1i5by22ivCBGIBbmF946GM3Uvrs9.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 47,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/tLXdJByTwZfPpjl7A7RPRabTfWz5xadv8iT9sOgb.png",
				"image2" => "images/2022-01-18/2QzR5T0GHRiOJ4hrr2lgIB1kjFId4SomsW0ea2tA.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 48,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-18/uQvAYYlqsozpScr3CqOAcwQj9o2MQNq0qZ2qt5sF.png",
				"image2" => "images/2022-01-18/YJc7Ju3unrJU6NzhcKZx6TgJ76hYi7sR16ls2lDz.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 49,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/D3NGOR8piiFJT0HeODnRs7Un1uVqf9uABC6VYVmv.png",
				"image2" => "images/2022-01-18/fF1t4WT4jfgSI7UsJKNg0RF0FnkPhORRgibtjo69.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 50,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/ZDieDuE6UYcLsGFBbMYOMqF3A4Qw7qVjeg5y4P3H.png",
				"image2" => "images/2022-01-18/6r3KyS2ea2rGqUB0mI1w0oF7zrMMeNuJuXaYcB7y.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 51,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-18/vvUf7vIcPlRkotdB1JFMGX3obKEPtsCFf0I5Y5ql.png",
				"image2" => "images/2022-01-18/k4LONYehJ0CKpYhBaIgehDhw2SkO00dhTAMuEC6S.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 52,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/yvRd4OPpfabttRhWNmkpSF9a04OjoXSDLvbGCT8r.png",
				"image2" => "images/2022-01-18/0J5HpYlhYkXbrfGcuRTdlXGp1VlDM8xMj1C9I0OM.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 53,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/48teM0Ng6QbRiiMjonSjjerbE2Dd1Q4FlwRlcAqn.png",
				"image2" => "images/2022-01-18/57hnvMwrSLGjmDtKffACc4JWbjZZIRj38VRmTu1N.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 54,
				"learning" => 0,
				"timeout" => 5,
				"image1" => "images/2022-01-18/BtlDOSgkiEaAaVZh32AiV4HOWaJ2TBauWPDkMutx.png",
				"image2" => "images/2022-01-18/XVv6GLpFfLSl8TYhjImu4IzbO2dZnh3W5abrzdtE.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 55,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/xUyrwZFyGryYm0yijXTccWkdGlHxLvGuJRU8cf5k.png",
				"image2" => "images/2022-01-18/5bDjqmeR2qjpSCKhanAu9FWKrjfH0TfOJDJvaWgH.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			],
			[
				"sort_no" => 56,
				"learning" => 1,
				"timeout" => 5,
				"image1" => "images/2022-01-18/QDs9KfpeaWWGHWGC81b0RNJjUDjsY5bTwodtBMrh.png",
				"image2" => "images/2022-01-18/YXuezYS7WdpnFQyTzyrFM3MQ3typ2etTcvXPTzDT.png",
				"value1" => "A+",
				"value2" => "B+",
				"set_id" => $set->getKey()
			]
		];
		$records = collect($questions)->map(function ($record) {
			return [
				'sort_no' => $record['sort_no'],
				'learning' => $record['learning'],
				'timeout' => $record['timeout'],
				'image1' => $record['image1'],
				'image2' => $record['image2'],
				'value1' => $record['value1'],
				'value2' => $record['value2'],
				'set_id' => $record['set_id'],
				'created_at' => now(),
				'updated_at' => now()
			];
		});
		Question::insert($records->toArray());
	}
}
