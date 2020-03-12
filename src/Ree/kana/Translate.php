<?php

namespace Ree\kana;

class Translate
{

	/** @var string */
	public const LANG_JA = 'ja';
	/** @var string */
	public const LANG_EN = 'en';

	/** @var string[] */
	private static $kana = [];

	/** @var bool */
	private static $initialized = false;

	public static function execute(string $text, string $source, string $target): string
	{
		foreach(self::$kana as $en => $ja){
			$text = str_replace($en, $ja, $text);
		}
		$text = rawurlencode($text);
		$ch = curl_init();
		curl_setopt(
			$ch,
			CURLOPT_URL,
			"https://script.google.com/macros/s/AKfycbweJFfBqKUs5gGNnkV2xwTZtZPptI6ebEhcCU2_JvOmHwM2TCk/exec?text={$text}&source={$source}&target={$target}"
		); //TODO: use https://www.google.co.jp/ime/cgiapi.html
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($ch);
		curl_close($ch);
		if($result === false){
			throw new TranslateException('cURL connection failed');
		}
		return $result;
	}

	public static function initialize(array $kana){
		if(self::$initialized){
			throw new TranslateException('Already initialized');
		}
		self::$kana = $kana;
	}
}