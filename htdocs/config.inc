<?php declare(strict_types=1);
function logException(Throwable $e) {
	global $account, $var, $player;
	$errorType = 'Unexpected Game Error!';
	$message = '';
	$delim = "\n\n-----------\n\n";

	if (is_object($account)) {
		$message .= 'Login: ' . $account->getLogin() . "\n" .
			'Account ID: ' . $account->getAccountID() . "\n" .
			'E-Mail: ' . $account->getEmail() . $delim;
	}
	$message .= 'Error Message: ' . $e . $delim;

	$message .= '$var: ' . var_export($var, true);

	// Don't display passwords input by users in the log message!
	if (isset($_REQUEST['password'])) {
		$_REQUEST['password'] = '*****';
	}
	$message .= "\n\n" . '$_REQUEST: ' . var_export($_REQUEST, true);
	$message .= $delim;

	$message .=
		'User IP: ' . getIpAddress() . "\n" .
		'USING_AJAX: ' . (defined('USING_AJAX') ? var_export(USING_AJAX, true) : 'undefined') . "\n" .
		'URL: ' . (defined('URL') ? URL : 'undefined');

	try {
		if (function_exists('release_lock')) {
			release_lock(); //Try to release lock so they can carry on normally
		}
	} catch (Throwable $ee) {
		$message .= $delim .
					'Releasing Lock Failed' . "\n" .
					'Message: ' . $ee . "\n";
	}

	if (defined('SCRIPT_ID')) {
		$message = 'Script: ' . SCRIPT_ID . $delim . $message . "\n\n";
	}

	// Unconditionally send error message to the log
	error_log($message);

	if (defined('NPC_SCRIPT') && ENABLE_DEBUG) {
		// In debug mode, we normally exit, but NPCs must cleanup after an error
		return;
	}

	if (ENABLE_DEBUG) {
		// Display error message on the page and then exit
		echo nl2br($message);
		exit;
	}

	// Send error message to the in-game auto bugs mailbox
	if (is_object($player) && method_exists($player, 'sendMessageToBox')) {
		$player->sendMessageToBox(BOX_BUGS_AUTO, $message);
	} elseif (is_object($account) && method_exists($account, 'sendMessageToBox')) {
		// Will be logged without a game_id
		$account->sendMessageToBox(BOX_BUGS_AUTO, $message);
	} else {
		// Will be logged without a game_id or sender_id
		SmrAccount::doMessageSendingToBox(0, BOX_BUGS_AUTO, $message, 0);
	}

	// Send error message to e-mail so that we have a permanent record
	if (!empty(BUG_REPORT_TO_ADDRESSES)) {
		$mail = setupMailer();
		$mail->Subject = (defined('PAGE_PREFIX') ? PAGE_PREFIX : '??? ') .
		                 'Automatic Bug Report';
		$mail->setFrom('bugs@smrealms.de');
		$mail->Body = $message;
		foreach (BUG_REPORT_TO_ADDRESSES as $toAddress) {
			$mail->addAddress($toAddress);
		}
		$mail->send();
	}

	return $errorType;
}

function handleException(Throwable $e) {
	// The real error message may display sensitive information, so we
	// need to catch any exceptions that are thrown while logging the error.
	try {
		$errorType = logException($e);
	} catch (Throwable $e) {
		error_log($e);
		$errorType = 'This error cannot be automatically reported. Please notify an admin!';
	}

	// If this is an ajax update, we don't really have a way to redirect
	// to an error page at this time, so we just quit.
	if (!defined('USING_AJAX') || !USING_AJAX) {
		header('location: /error.php?msg=' . urlencode($errorType));
	}
	exit;
}

/**
 * Can be used to convert any type of notice into an exception.
 */
function exception_error_handler($errno, $errstr, $errfile, $errline) {
	throw new ErrorException($errstr, $errno, E_ERROR, $errfile, $errline);
}

function setupMailer() {
	$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
	if (!empty(SMTP_HOSTNAME)) {
		$mail->isSMTP();
		$mail->Host = SMTP_HOSTNAME;
	}
	return $mail;
}

function getIpAddress() {
	foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
					return $ip;
				}
			}
		}
	}
	return 'unknown';
}

function dumpMemDiff($msg) {
	static $memory;
	@ob_end_clean();
	var_dump($msg);
	var_dump(($memory2 = memory_get_usage()) - $memory);
	$memory = $memory2;
	ob_start();
}

/**
 * Wrapper around the floor() builtin for returning an integer type.
 */
function IFloor(float $val) : int {
	return (int)floor($val);
}

/**
 * Wrapper around the ceil() builtin for returning an integer type.
 */
function ICeil(float $val) : int {
	return (int)ceil($val);
}

/**
 * Wrapper around the round() builtin for returning an integer type.
 */
function IRound(float $val) : int {
	return (int)round($val);
}

/**
 * Generate a cryptographically strong random hexadecimal string.
 * The requested length must be a multiple of 2.
 */
function random_string(int $length) : string {
	if ($length % 2 != 0) {
		throw new Exception('Length must be a multiple of 2!');
	}
	return bin2hex(random_bytes($length / 2));
}

// Defines all constants
require_once('config.php');

// Set up vendor and class autoloaders
require_once(ROOT . 'vendor/autoload.php');
require_once(LIB . 'autoload.inc');
spl_autoload_register('get_class_loc');
