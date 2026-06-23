<?php
	$mysqli = mysqli_connect("localhost","iut_s203","mdp","s203");

	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
	}



	$utilisateurCree = false;

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$email = $_POST['email'];
		$dateNaissance = $_POST['dateNaissance'];
		$login = $_POST['login'];
		$password = $_POST['password'];


		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		$id = generateID();


		$query = "
			INSERT INTO user (id, email, login, password, dateNaissance)
			VALUES ('$id', '$email', '$login', '$passwordHash', '$dateNaissance')
		";

		if (mysqli_query($mysqli, $query)) {
			$utilisateurCree = true;
		} else {
			echo "Error: " . $query . "<br>" . mysqli_error($mysqli);
		}
	}


	function generateID() {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$id = '';
		for ($i = 0; $i < 8; $i++) {
			$id .= $characters[mt_rand(0, strlen($characters) - 1)];
		}
		return $id;
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
		<h1 class="txt-center">Inscription</h1>


		<?php 
			if ($utilisateurCree) {
				echo "<p class='success'>Utilisateur créé avec succès !</p>";
			}
		?>
		
		<form method="post" action="signup.php" id="saisiesCredentials" class="d-flex flex-col gap-1">


            <div class="d-flex flex-jc-between input">
				<p>Email :</p>
				<input
                    type="email"
                    name="email"
                    required
                    placeholder="jean.dupont@gmail.com"
                >
			</div>

            <div class="d-flex flex-jc-between input">
				<p>Date de naissance :</p>
				<input
                    type="date"
                    name="dateNaissance"
                    required
                    placeholder="jean.dupont@gmail.com"
                >
			</div>

			<div class="d-flex flex-jc-between input">
				<p>Pseudo :</p>
				<input
                    type="text"
                    name="login"
                    required
                    placeholder="JDupont"
                >
			</div>

			<div class="d-flex flex-jc-between input">
				<p>Mot de passe :</p>
				<input
                    type="password"
                    name="password"
                    required
                    minlength="8"
                >
			</div>

            <div class="d-flex gap-1 input">
                <input
                    type="checkbox"
                    name="check"
                    class="notInput"
                >

				<p>J'accepte de m'inscrire</p>
			</div>
		</form>



		<input
			type="submit"
			form="saisiesCredentials"
			value="S'inscrire"
			class="buttonConnect"
		>

		<hr>
		
		<a 
			class="connectpage d-flex flex-jc-center flex-ai-center"
			href="login.php"
		>
			Se connecter
		</a>

	</main>

</body>

</html>