<?php

namespace Ree\kana;

class Translate
{

	/** @var string[] */
	private $kana = [];

	public function __construct(array $kana){
		$this->kana = $kana;
	}

	public function execute(string $text): string
	{
		foreach($this->kana as $en => $ja){
			$text = str_replace($en, $ja, $text);
		}
		$text = rawurlencode($text);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.google.com/transliterate?langpair=ja-Hira|ja&text={$text}");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		if($result === false){
			throw new TranslateException('cURL connection failed');
		}
		$resultText = '';
		foreach(json_decode($result, true) as $content){
			$resultText .= $content[1][0];
		}
		return $resultText;
	}
}