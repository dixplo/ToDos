<?php

namespace component;

use Ajax\semantic\html\base\constants\Color;
use models\Slate;
use models\Template;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\USession;

class ListAll
{

	public static $semantic;

	public static function home()
	{
		$tab = self::$semantic->htmlTab("tabMenu", [["Todos", self::listToDo()], ["Templates", self::listTemplate()], ["Account", self::account()]]);
		$tab->setPointing()->setSecondary();
		return $tab;
	}

	/**
	 * listToDo
	 *
	 * @return void
	 */
	public static function listToDo()
	{
		$user = USession::get("currentUser");
		$slate = DAO::getAll(Slate::class, "idUser='{$user->getId()}'");
		$containers = ['<div class="classe">'];

		foreach ($slate as $list) {
			$items = $list->getItems();

			$card = self::$semantic->htmlCard($list->getId());
			$card->addItemHeaderContent($list->getTitle(), count($items), $list->getDescription());
			$card->asLink("todo/editSlate/" . $list->getId());
			$card->addClass("todo");
			$card->setTagName('a');
			
			if ($list->getTemplate()->getId()==2) {
				$nbCheked =0;
				foreach ($items as $item) {
					if ($item->getChecked() == 1) {
						$nbCheked++;
					}
				}
				$pb = self::$semantic->htmlProgress($list->getId() . "progressBar");
				$nbitems = (count($items) > 0) ? $nbCheked / count($items) * 100 : 0;
				$pb->setPercent($nbitems);
				$pb->setIndicating();
				$pb->setTotal(count($items));
				$pb->setTextValues(["active" => "", "success" => ""]);
				$card->addExtraContent($pb);
			}

			array_push($containers, $card);
		}
		array_push($containers, '</div>');
		return $containers;
	}

	public static function listTemplate()
	{
		$user = USession::get("currentUser");

		$templateUser = DAO::getAll(Template::class, "idUser='{$user->getId()}'");
		$templateCommon = DAO::getAll(Template::class, "idUser IS NULL");
		$containers = self::makeList($templateUser);
		$containers[] = self::$semantic->htmlDivider("");
		array_push($containers, self::makeList($templateCommon));

		return $containers;
	}
	private static function makeList($template)
	{
		$containers = ['<div class="classe">'];

		foreach ($template as $list) {

			$card = self::$semantic->htmlCard($list->getId());
			$card->addItemHeaderContent($list->getName(), "", $list->getDescription());
			//$card->asLink("todo/editSlate/".$list->getId());
			$card->addClass("template");
			$card->setTagName('a');
				        
	        array_push($containers, $card);
		}
		array_push($containers, '</div>');
		return $containers;
	}

	// Fonction account 
	public static function account()
	{
		$containers = ['<div>'];

		$user = USession::get("currentUser");
		$card = self::$semantic->htmlCard("card1");
		$card->addImage("https://semantic-ui.com/images/avatar2/large/kristy.png");
		$card->addItemHeaderContent("Kristy", "Joined in 2013", "Kristy is an art director living in New York.");
		$card->addExtraContent("22 Friends")->addIcon("user");
		
		$containers[]=$card;
		array_push($containers, '</div>');
		return $containers;
	}
}
