<?php

namespace comm;

/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2018/1/2
 * Time: 上午11:33
 */
class Rules
{
	static $cnPhoneRule;
	static $twPhoneRule;

	public static function filter ($list, $rules)
	{
		global $config;

		$result = [ ];
		$filter = [ ];
		foreach ($list as $value) {
			$isPass = true;
			foreach ($rules as $rule => $key) {
				switch ($rule) {
					case 'require':
						if (is_null($value) || empty( $value ) || !isset( $value )) {
							$isPass = false;
							$filter[ $value ] = $rule;
						}
						break;
					case 'length':
						if (strlen($value) < $key) {
							$isPass = false;
							$filter[ $value ] = $rule;
						}
						break;
					case 'begin':
						if (strpos($value, $key) !== 0) {
							$isPass = false;
							$filter[ $value ] = $rule;
						}
						break;
					case 'phone_rule':
						// 依電話規則過濾電話
						// 有符合算pass
						$cnPhoneRule = self::$cnPhoneRule ?? json_decode(file_get_contents($config->base[ "cn_phone_rule" ]), true);
						$twPhoneRule = self::$twPhoneRule ?? json_decode(file_get_contents($config->base[ "tw_phone_rule" ]), true);
						self::$cnPhoneRule = $cnPhoneRule;
						self::$twPhoneRule = $twPhoneRule;

						$isPass = false;
						$filter[ $value ] = $rule;
						$valLen = strlen($value);
						for ($len = 2; $len < $valLen; $len++) {
							$subNum = substr($value, 0, $len);
							if ($cnPhoneRule[ $subNum ]) {
								if ($valLen == $cnPhoneRule[ $subNum ]) {
									$isPass = true;
									unset( $filter[ $value ] );
									$len += $valLen;
								}
							} else if ($twPhoneRule[ $subNum ]) {
								if ($valLen == $twPhoneRule[ $subNum ]) {
									$isPass = true;
									unset( $filter[ $value ] );
									$len += $valLen;
								}
							}
						}
						break;
				}
			}
			if ($isPass) {
				$result[] = $value;
			}
		}
//		\Console::dd([
//			'result' => $result,
//			'filter' => $filter
//		]);
//		die();

		return [
			'result' => $result,
			'filter' => $filter
		];
	}
}
