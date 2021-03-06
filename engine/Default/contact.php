<?php declare(strict_types=1);

$template->assign('PageTopic', 'Contact Form');

$container = create_container('contact_processing.php');
$template->assign('ProcessingHREF', SmrSession::getNewHREF($container));

$template->assign('From', $account->getLogin());
