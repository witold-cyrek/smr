<?php declare(strict_types=1);

$message = '<div class="center">';

if ($player->getCredits() < 10) {
	create_error('Come back when you get some money!');
}
$player->decreaseCredits(10);

if (isset($var['action']) && $var['action'] != 'drink') {
	$drinkName = 'water';
	$message .= 'You ask the bartender for some water and you quickly down it.<br />You don\'t feel quite so intoxicated anymore.<br />';
	$db->query('DELETE FROM player_has_drinks WHERE ' . $player->getSQL() . ' LIMIT 1');
	$player->increaseHOF(1, array('Bar', 'Drinks', 'Water'), HOF_PUBLIC);
} else {
	$random = mt_rand(1, 20);
	//only get Azool or Spock drink if they are very lucky
	if ($random != 1) {
		$db->query('SELECT drink_id, drink_name FROM bar_drink WHERE drink_id != 1 && drink_id != 11 ORDER BY rand() LIMIT 1');
	} else {
		$db->query('SELECT drink_id, drink_name FROM bar_drink ORDER BY rand() LIMIT 1');
	}

	if ($db->nextRecord()) {
		$drinkName = $db->getField('drink_name');
		$drink_id = $db->getInt('drink_id');

		$db->query('SELECT drink_id FROM player_has_drinks WHERE game_id = ' . $db->escapeNumber($player->getGameID()) . ' ORDER by drink_id DESC LIMIT 1');
		if ($db->nextRecord()) {
			$curr_drink_id = $db->getInt('drink_id') + 1;
		} else {
			$curr_drink_id = 1;
		}

		if ($drink_id != 11 && $drink_id != 1) {
			$message .= ('You have bought a ' . $drinkName . ' for $10');
			$db->query('INSERT INTO player_has_drinks (account_id, game_id, drink_id, time) VALUES (' . $db->escapeNumber($player->getAccountID()) . ', ' . $db->escapeNumber($player->getGameID()) . ', ' . $db->escapeNumber($curr_drink_id) . ', ' . $db->escapeNumber(TIME) . ')');
			$player->increaseHOF(1, array('Bar', 'Drinks', 'Alcoholic'), HOF_PUBLIC);
		} else {
			$message .= ('The bartender says, Ive got something special for ya.<br />');
			$message .= ('The bartender turns around for a minute and whips up a ' . $drinkName . '.<br />');

			if ($drink_id == 1) {
				$message .= ('The bartender says that Spock himself gave him the directions to make this drink.<br />');
			}

			$message .= ('You drink the ' . $drinkName . ' and feel like like you have been drinking for hours.<br />');

			if ($drink_id == 11) {
				$message .= ('After drinking the ' . $drinkName . ' you feel like nothing can bring you down and like you are the best trader in the universe.<br />');
			}

			//has the power of 2 drinks
			$db->query('INSERT INTO player_has_drinks (account_id, game_id, drink_id, time) VALUES (' . $db->escapeNumber($player->getAccountID()) . ', ' . $db->escapeNumber($player->getGameID()) . ', ' . $db->escapeNumber($curr_drink_id) . ', ' . $db->escapeNumber(TIME) . ')');
			$curr_drink_id++;
			$db->query('INSERT INTO player_has_drinks (account_id, game_id, drink_id, time) VALUES (' . $db->escapeNumber($player->getAccountID()) . ', ' . $db->escapeNumber($player->getGameID()) . ', ' . $db->escapeNumber($curr_drink_id) . ', ' . $db->escapeNumber(TIME) . ')');
			$player->increaseHOF(1, array('Bar', 'Drinks', 'Special'), HOF_PUBLIC);
		}

	}
	$db->query('SELECT count(*) FROM player_has_drinks WHERE ' . $player->getSQL());
	$db->requireRecord();
	$num_drinks = $db->getInt('count(*)');
	//display woozy message
	$message .= '<br />You feel a little W' . str_repeat('oO', $num_drinks) . 'zy<br />';
}

$player->actionTaken('BuyDrink', array(
	'SectorID' => $sector->getSectorID(),
	'Drink' => $drinkName
));

//see if the player blacksout or not
if (isset($num_drinks) && $num_drinks > 15) {
	$percent = mt_rand(1, 25);
	$lostCredits = IRound($player->getCredits() * $percent / 100);

	$message .= '<span class="red">You decide you need to go to the restroom.  So you stand up and try to start walking but immediately collapse!<br />About 10 minutes later you wake up and find yourself missing ' . number_format($lostCredits) . ' credits</span><br />';

	$player->decreaseCredits($lostCredits);
	$player->increaseHOF(1, array('Bar', 'Robbed', 'Number Of Times'), HOF_PUBLIC);
	$player->increaseHOF($lostCredits, array('Bar', 'Robbed', 'Money Lost'), HOF_PUBLIC);

	$db->query('DELETE FROM player_has_drinks WHERE ' . $player->getSQL());

}
$player->increaseHOF(1, array('Bar', 'Drinks', 'Total'), HOF_PUBLIC);
$message .= '</div>';

$container = create_container('skeleton.php', 'bar_main.php');
transfer('LocationID');
$container['message'] = $message;
forward($container);
