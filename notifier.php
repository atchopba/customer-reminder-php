<?php
include_once("config.php");

$notif_email_model = file_get_contents("model/notification-email.model.txt");
$notif_sms_model = file_get_contents("model/notification-sms.model.txt");

/**
 * Renvoie de la date + heure à la seconde près
 * @return 
 */
function get_current_timestamp() {
	return date("Y-m-d H:i:s");
}

/**
 * Renvoie de la date + heure + min
 * @return 
 */
function get_current_date_time() {
	return date("Y-m-d H:i");
}

/**
 * Ajout d'un message dans les logs
 * @param message 	Message
 */
function add_2_log($message) {
	file_put_contents(__log_file__, get_current_date_time()." 	". $message. "\n", FILE_APPEND | LOCK_EX); // en appliquant un file lock (Php ver 5.1 en montant) on s'assure de l'exclusivité du fichier pendant les file open.
}

/**
 * renvoie des clients à notifier
 * @return array
 */
function get_customer_2_notify($query) {
	$mysqli = new mysqli(__host__, __user__, __pwd__, __db__);
	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	$arr_customers = array();
	if ($result = $mysqli->query($query)) {
	    /* fetch object array */
	    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	        $arr_customers[] = $row;
	    }
	    /* free result set */
	    $result->close();
	}
	/* close connection */
	$mysqli->close();
	// 
	return $arr_customers;
}

/**
 * Exécution de la requête d'insertion
 * @param $id_ 		ID du client
 * @param $query 	Requête
 */
function execute_insert_query($id_, $query) {
	// Create connection
	$mysqli = new mysqli(__host__, __user__, __pwd__, __db__);
	// Check connection
	if ($mysqli->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	} 

	if ($mysqli->query($query) === TRUE) {
	    add_2_log("Enregistrement succès en BDD du client $id_");
	} else {
	    add_2_log("Enregistrement error en BDD du client $i => query => $query => error => ".  $mysqli->error);
	}

	$mysqli->close();
}

/**
 * Ajout de la relance
 * @param $id_ 			ID du client
 * @param $email 		email envoyé
 * @param $sms 			sms oui/non			
 * @param $sms_report 	
 */
function add_relance_historique($id_, $email, $sms, $sms_report) {
	$query = "INSERT INTO relance_historique (id_portfolio, email, sms, sms_report) VALUES ".
			"('$id_', '$email', '$sms', '$sms_report')";
	execute_insert_query($id_, $query);
}

/**
 * Envoi d'un mail
 * @param $id_
 * @param $to 		Destinataire du mail
 * @param $subject 	Sujet
 * @param $message 	Message à envoyer
 */
function send_mail($id_, $to, $subject, $message) {
	$headers = 'From: Digital Experience SARL <'. __adrmail_from__ .'>' . "\r\n" .
	'Reply-To: ' . __adrmail_from__ . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

	@mail($to, $subject, $message, $headers);
	add_2_log("Envoi du mail du client $id_");
}

/**
 * Send notification to customers
 * @param $notif_email_model 	Modele de mail
 * @param $notif_sms_model 		Modele de sms
 * @param $customers 			Tableau de clients
 */
function notify_customers($notif_email_model, $notif_sms_model, $customers) {
	// parcours des customers
	for ($i=0; $i<count($customers); $i++) {

		$c = $customers[$i];
		// paramétrage de la notification
		$notif = str_replace("#date_courante#", get_current_date_time(), $notif_email_model);
		$notif = str_replace("#nom_client#", $c["nom_client"], $notif);
		$notif = str_replace("#date_expiration#", $c["date_expiration"], $notif);
		$notif = str_replace("#nom_domaine#", $c["nom_domaine"], $notif);
		// 
		$email_sent = "non";
		// envoie du mail
		if ($c["email_contact"] != null && $c["email_contact"] != "") {
			send_mail($c["id"], $c["email_contact"], __subject_mail__, $notif);	
			$email_sent = "oui";
			// ajout dans la bdd
			add_relance_historique($c["id"], $email_sent, "non", null);
		}
		// envoie du sms
		if ($c["sms_relance"] == "oui" && $c["numero_tel"] != null && $c["numero_tel"] != "") {
			// ===> sms envoyé <===
			// ===> utiliser $notif_sms_model pour envoyer le sms
			$id_ = $c["id"];
			add_2_log("Envoi du sms du client $id_");
			// ajout dans la bdd
			add_relance_historique($c["id"], $email_sent, "oui", null);
		}
		
	}
}

// selection des clients de 4 semaines
$query = "SELECT * FROM portolio WHERE DATEDIFF(date_expiration, CURRENT_DATE) = 28 AND payement_effectue <> 'oui'";
$arr_customers = get_customer_2_notify($query); 
notify_customers($notif_email_model, $notif_sms_model, $arr_customers);

// selection des clients de 3 semaines
$query = "SELECT * FROM portolio WHERE DATEDIFF(date_expiration, CURRENT_DATE) = 21 AND payement_effectue <> 'oui'";
$arr_customers = get_customer_2_notify($query);
notify_customers($notif_email_model, $notif_sms_model, $arr_customers);

// selection des clients de 2 semaines
$query = "SELECT * FROM portolio WHERE DATEDIFF(date_expiration, CURRENT_DATE) = 14 AND payement_effectue <> 'oui'";
$arr_customers = get_customer_2_notify($query);
notify_customers($notif_email_model, $notif_sms_model, $arr_customers);

// selection des clients de 1 semaines
$query = "SELECT * FROM portolio WHERE DATEDIFF(date_expiration, CURRENT_DATE) = 7 AND payement_effectue <> 'oui'";
$arr_customers = get_customer_2_notify($query);
notify_customers($notif_email_model, $notif_sms_model, $arr_customers);

// selection des clients de 3 jours
$query = "SELECT * FROM portolio WHERE DATEDIFF(date_expiration, CURRENT_DATE) = 3 AND payement_effectue <> 'oui'";
$arr_customers = get_customer_2_notify($query);
notify_customers($notif_email_model, $notif_sms_model, $arr_customers);

// selection des clients de 1 jour
$query = "SELECT * FROM portolio WHERE DATEDIFF(date_expiration, CURRENT_DATE) = 1 AND payement_effectue <> 'oui'";
$arr_customers = get_customer_2_notify($query);
notify_customers($notif_email_model, $notif_sms_model, $arr_customers);

?>
