<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
date_default_timezone_set('Europe/Paris');


//Etape 1

function sendMail($subject, $pMailTo, $pMessage, $pMailToBcc = true){
	require 'config.php';

	$transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
  	->setUsername($setUsername)
  	->setPassword($setPassword)
	;

	$mailer = new Swift_Mailer($transport);

	if (!is_array($pMailTo)) {
		$pMailTo = [$pMailTo];
	}
  	$message = (new Swift_Message($subject));
  	$message->setFrom([$setUsername => $pseudo]);

  	if ($pMailToBcc == true){
  		$message->setBcc($pMailTo);
  	}else{
  		$message->setTo($pMailTo);
  	}

  	if (is_array($pMessage) && array_key_exists("html", $pMessage) && array_key_exists("text", $pMessage)) {
		$message->setBody($pMessage["html"], 'text/html');
		$message->addPart($pMessage["text"], 'text/plain');

	}elseif (is_array($pMessage) && array_key_exists("html", $pMessage)) {
		$message->setBody($pMessage["html"], 'text/html');
		$message->addPart($pMessage["html"], 'text/plain');

	}elseif (is_array($pMessage) && array_key_exists("text", $pMessage)) {
		$message->setBody($pMessage["text"], 'text/plain');

	}elseif (is_array($pMessage)) {
		die('erreur une clé n\'est pas bonne');

	}else{
		$message->setBody($pMessage, 'text/plain');
	}
	return $mailer->send($message);
}

if (session_status() != PHP_SESSION_ACTIVE){
	session_start();
}

if (isset($_SESSION["mail"]) && strtolower($_SESSION['mail']) == 'ok') {
	$subject = "Connection sur votre site";
	$mailto = "chayannick@hotmail.fr";
	$token = substr(md5(time()), 0, 12);
	$sendMail = ["html" => '<h1>Connection sur votre site</h1><p>Une personne s\'est connectée sur votre site.</p></ br><p>le token est : '.$token.'</p>'];
	sendMail($subject, $mailto, $sendMail);
	echo "mail envoyé!</ br>";
	fopen($token.'.php', 'w');
	echo "fichier token.php crée!</ br>";
	unset($_SESSION["mail"]);
}else{
	$_SESSION["mail"] = 'ok';
	echo "Rafraichir la page pour votre surprise";
}

