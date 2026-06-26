<?php
	session_start();

	$mysqli = mysqli_connect("localhost","iut_s203","mdp","s203");

	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$login = $_POST["login"];

		$query = "SELECT COUNT(*), password, id FROM user WHERE login = '".$login."'";
		$result = mysqli_query($mysqli, $query);
		$row = mysqli_fetch_row($result);

		if ($row[0] == 1) {
			if (password_verify($_POST["password"], $row[1])) {
				
				$timestamp = time() + (60 * 60); // 1 heure
				$query = "INSERT INTO sessions (user_id, session_id, expiration_date) VALUES ('".$row[2]."', '".session_id()."', $timestamp) ON DUPLICATE KEY UPDATE session_id = '".session_id()."', expiration_date = $timestamp";
				mysqli_query($mysqli, $query);

				

				header('Location: https://regabillard.fr/IUT/s203/profil.php');
  				exit();
			} else {
				$txtError = "Le mot de passe est incorrect.";
			}
		} else {
			$txtError = "L'identifiant est incorrect.";
		}
	}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="css/common.css">
	<link rel="stylesheet" href="css/login.css">

	<title>Connexion</title>
</head>

<body>
	<main class="d-flex flex-col flex-jc-c gap-2">


		<?php 

			if ($txtError) {
				echo "<p class='error'>".$txtError."</p>";
			}

		?>

		<form method="post" action="login.php" id="saisiesCredentials" class="d-flex flex-col gap-1">

			<h1 class="txt-center">Connexion</h1>

			<div id="saisiesCredentials" class="d-flex flex-col gap-1">

				<div class="d-flex flex-jc-between input">
					<p>Pseudo :</p>
					<input type="text" name="login" required>
				</div>

				<div class="d-flex flex-jc-between input">
					<p>Mot de passe :</p>
					<input type="password" name="password" required minlength="8">
				</div>
			</div>

		</form>
		
		<input
			type="submit"
			form="saisiesCredentials"
			value="Se connecter"
			class="buttonConnect"
		>

		<hr>
		
		<a 
			class="connectpage d-flex flex-jc-center flex-ai-center"
			href="signup.php"
		>
			Créer un compte
		</a>

	</main>

</body>

</html>