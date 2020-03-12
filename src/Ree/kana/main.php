<?php

namespace Ree\kana;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use Ree\kana\task\TranslateTask;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerChatEvent;

class main extends PluginBase implements Listener
{

	/** @var Translate */
	private $translate;

	public function onEnable()
	{
		$file = $this->getResource('kana.json');
		$json = '';
		while (($line = fgets($file))) {
			$json .= $line;
		}
		fclose($file);
		$this->translate = new Translate(json_decode($json, true));
		$this->getLogger()->info("このプラグインは開発段階なため,動作は保証できません");
		$this->getLogger()->info("バグ報告はこちらへ");
		$this->getLogger()->info("https://github.com/Ree-jp/kana/issues");
		$this->getLogger()->warning('----------------------------------------');
		$this->getLogger()->info("This plugin has been converted using google's translation api, so the meaning may be different from the sentence before conversion");
		$this->getLogger()->warning('----------------------------------------');
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onChat(PlayerChatEvent $ev)
	{
		$bool = $this->isChange($ev->getPlayer());
		if ($bool) {
			$oldMessage = $ev->getMessage();
			Server::getInstance()->getAsyncPool()->submitTask(new TranslateTask('<'.$ev->getPlayer()->getDisplayName().'>', $oldMessage, $this->translate));
			$ev->setCancelled();
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		if ($command == "kana") {
			if (!$sender instanceof Player) {
				$sender->sendMessage("§6>> §rコンソールでは使用不可なコマンドです");
				return false;
			}
			$bool = $this->isChange($sender);
			if (!isset($args[0])) {
				if ($bool) {
					$args[0] = "off";
				} else {
					$args[0] = "on";
				}
			}
			if ($args[0] === "on") {
				if ($bool) {
					$sender->sendMessage("§6>> §rすでにonになっています");
				} else {
					$nbt = $sender->namedtag;
					$nbt->setInt("kanaChange", 1);
					$sender->sendMessage("§a>> §r自動変換機能を有効にしました");
				}
			} elseif ($args[0] == "off") {
				if ($bool) {
					$nbt = $sender->namedtag;
					$nbt->setInt("kanaChange", 0);
					$sender->sendMessage("§a>> §r自動変換機能を無効にしました");
				} else {
					$sender->sendMessage("§6>> §rすでにoffになっています");
				}
			} else {
				return false;
			}
		}
		return false;
	}

	private function isChange(Player $p): bool
	{
		$nbt = $p->namedtag;
		if ($nbt->offsetExists("kanaChange")) {
			if ($nbt->getInt("kanaChange")) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}