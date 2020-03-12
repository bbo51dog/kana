<?php

namespace Ree\kana\task;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Ree\kana\Translate;
use Ree\kana\TranslateException;

class TranslateTask extends AsyncTask
{

	/** @var string */
	private const BAD_REQUEST_PREFIX = '[Translate Bad Request] ';

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var Translate
	 */
	private $translate;

	public function __construct(string $name, string $text, Translate $translate)
	{
		$this->name = $name;
		$this->text = $text;
		$this->translate = $translate;
	}

	/**
	 * @inheritDoc
	 */
	public function onRun()
	{
		try
		{
			$result = $this->translate->execute($this->text);
		} catch (TranslateException $e) {
			$result = self::BAD_REQUEST_PREFIX.$this->text;
		}
		$this->setResult($result);
	}

	/**
	 * @param Server $server
	 */
	public function onCompletion(Server $server)
	{
		$server->broadcastMessage($this->name . ' ' . $this->getResult() . TextFormat::GOLD. '   <' . $this->getResult() . '>');
		parent::onCompletion($server);
	}
}