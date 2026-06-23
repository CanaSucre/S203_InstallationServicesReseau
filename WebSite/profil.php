<?php
	session_start();
	
	$mysqli = mysqli_connect("localhost","iut_s203","mdp","s203");

	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
	}


	$user = $_GET['user'];
	if (!$user) {
		$query = "SELECT user_id FROM sessions WHERE session_id = '".session_id()."' AND expiration_date > ".time();
		$result = mysqli_query($mysqli, $query);
		$row = mysqli_fetch_row($result);

		if ($row) {
			$user = $row[0];
		} else {
			header('Location: https://regabillard.fr/IUT/s203/login/login.php');
			exit();
		}
	}

	$query = "SELECT * FROM user WHERE id = '$user'";

	$result = mysqli_query($mysqli, $query);
    $row = mysqli_fetch_row($result);

    $id = $row[0];
    $accountType = $row[1];
    $login = $row[3];
    $dateNaissance = $row[5];
    $isVerified = $row[6];
   	$registeredAt = $row[7];
    $lastLogin = $row[8];
    $grade = $row[9];
	$isDeleted = $row[10];
	

	$age = floor((time() - strtotime($dateNaissance)) / (365.25 * 24 * 60 * 60));

	if ($grade == "PLAYER") {
		$grade = "Joueur";
	} else if ($grade == "ADMIN") {
		$grade = "Administrateur";
	}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="css/common.css">
	<link rel="stylesheet" href="css/profil.css">

	<title>Profil de <?php echo $login; ?></title>
</head>

<body>
	<main>
		<h1 class="txt-center" id="titleProfil">
			Profil de : 
			<?php 
				echo $login;
				
				if ($isDeleted) {
					echo " (Compte supprimé)";
				}
			?>
		</h1>



		<section class="d-flex gap-1">

			<!-- Identification du joueur -->
			<article id="identification" class="fond p-1 brad-0_5">
				<h2 class="m-0 m-b-1">Identification</h2>

				<div class="d-flex gap-1_5">
					<div id="photoProfil"></div>

					<div class="d-flex flex-col">
						<p class="m-0"><?php echo $login; ?></p>
						<p class="m-0"><?php echo $age; ?> ans</p>
						<p class="italic m-0 m-t-1">#<?php echo $id; ?></p>
					</div>
				</div>
			</article>

			<!-- Autres informations sur le joueur -->

			<article id="otherInfo" class="fond p-1 brad-0_5">
				<h2 class="m-0 m-b-1">Informations du compte</h2>

				<div class="d-flex gap-1_5">
					<div class="d-flex flex-col">
						<p class="m-0"><strong>Date d'inscription : </strong>
							<?php 
								echo date('m/d/Y', strtotime($registeredAt));
							?>
						</p>
						<p class="m-0"><strong>Dernière connexion : </strong>
							<?php
								echo date('m/d/Y', strtotime($lastLogin));
							?>
						</p>
						<p class="m-0"><strong>Grade : </strong><?php echo $grade;?></p>
						<p class="m-0"><strong>Vérifié : </strong>
							<?php
								if ($isVerified) {
									echo "Oui";
								} else {
									echo "Non";
								}
							?>
						</p>
					</div>
				</div>
			</article>

			<!-- Statistiques du joueur -->
			<article id="statistiques" class="fond p-1 brad-0_5">
				<h2 class="m-0 m-b-1">Statistiques</h2>

				<div class="d-flex gap-0_5 flex-jc-around">
					<div class="fond statItem txt-center brad-0_5 p-0_5 d-flex flex-col flex-jc-around">
						<p class="m-0 statValue" id="partiesJouees">
							<?php 
								$query = "SELECT COUNT(*) FROM player_game WHERE player_id = '$id'";
								$result = mysqli_query($mysqli, $query);
								$row = mysqli_fetch_row($result);
								$nbParty = $row[0];
								echo $nbParty;
							?>
						</p>
						<p class="m-0 statTitle">Partie<?php echo $nbParty > 1 ? 's' : ''; ?> jouée<?php echo $nbParty > 1 ? 's' : ''; ?></p>
					</div>
					<div class="fond statItem txt-center brad-0_5 p-0_5 d-flex flex-col flex-jc-around">
						<p class="m-0 statValue" id="victoires">
							<?php 
								$query = "SELECT COUNT(*) FROM game WHERE winner = '$id'";
								$result = mysqli_query($mysqli, $query);
								$row = mysqli_fetch_row($result);
								$nbWins = $row[0];
								echo $nbWins;
							?>
						</p>
						<p class="m-0 statTitle">Victoire<?php echo $nbWins > 1 ? 's' : ''; ?></p>
					</div>
					<div class="fond statItem txt-center brad-0_5 p-0_5 d-flex flex-col flex-jc-around">
						<p class="m-0 statValue" id="defaites">
							<?php 
								$nbDefeats = $nbParty - $nbWins;
								echo $nbDefeats;
							?>
						</p>
						<p class="m-0 statTitle">Défaite<?php echo $nbDefeats > 1 ? 's' : ''; ?></p>
					</div>
					<div class="fond statItem txt-center brad-0_5 p-0_5 d-flex flex-col flex-jc-around">
						<p class="m-0 statValue" id="tauxVictoire">
							<?php 
								$tauxVictoire = $nbParty > 0 ? round(($nbWins / $nbParty) * 100) : 0;
								echo $tauxVictoire . '%';
							?>
						</p>
						<p class="m-0 statTitle">Taux de victoire</p>
					</div>
				</div>
			</article>
		</section>


		<section class="m-t-2 d-flex gap-1 flex-jc-between">
			<article id="historiqueParties" class="fond p-1 brad-0_5">
				<h2 class="m-0">Historique des parties</h2>

				<?php 
					$query = "
						SELECT *
						FROM game
						INNER JOIN player_game ON game.id = player_game.game_id
						LEFT JOIN user ON winner = user.id
						WHERE player_game.player_id = '$id'
						ORDER BY game.dateStart DESC
					";

					$result = mysqli_query($mysqli, $query);


				?>

				<table class="m-t-2">
					<thead>
						<tr>
							<th>Date</th>
							<th>Adversaires</th>
							<th>Résultat</th>
							<th>Vainqueur</th>
							<th>Durée</th>
							<th>Statut</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$row = mysqli_fetch_row($result);
							while ($row) {
								$date = date('d/m/Y', strtotime($row[3]));

								$fin = strtotime($row[4]);

								if (!$fin) {
									$fin = time();
								}

								if ($row[2] == 'CANCELED') {
									$duree = "N/A";
								} else {
									$duree = floor(($fin - strtotime($row[3])) / 60)." min";
								}

								// Récupérer les adversaires
								$idGame = $row[0];
								$queryAdversaires = "
									SELECT user.login
									FROM player_game
									INNER JOIN user ON player_game.player_id = user.id
									WHERE player_game.game_id = '$idGame' AND player_game.player_id != '$id'
								";
								$resultAdversaires = mysqli_query($mysqli, $queryAdversaires);
								$adversaires = [];
								while ($rowAdversaire = mysqli_fetch_row($resultAdversaires)) {
									$adversaires[] = $rowAdversaire[0];
								};
								$adversaires = implode(' | ', $adversaires);

								// Déterminer le résultat et le statut de la partie
								if ($row[2] == 'IN_PROGRESS') {
									$resultat = "N/A";
									$class = "";
									$statut = "En cours";
								} else if ($row[2] == 'CANCELED') {
									$resultat = "N/A";
									$class = "";
									$statut = "Annulée";
								} else {
									$statut = "Terminée";
									if ($row[1] == $id) {
										$resultat = "Victoire";
										$class = "victory";
									} else {
										$resultat = "Défaite";
										$class = "defeat";
									} 
								}

								// Afficher la partie dans le tableau
								echo "
									<tr class='$class'>
										<td>$date</td>
										<td>$adversaires</td>
										<td>$resultat</td>
										<td>$row[10]</td>
										<td>$duree</td>
										<td>$statut</td>
									</tr>
								";

								$row = mysqli_fetch_row($result);
							}
						?>
					
					</tbody>
				</table>
			</article>

			<div class="d-flex flex-col gap-1">
				<article id="infosDynamiques" class="fond p-1 brad-0_5">
					<h2 class="m-0">Vos informations dynamiques</h2>

					<p class="m-0 m-t-2"><strong>Date du serveur : </strong>
						<?php 
							echo date('m/d/Y H:i:s');
						?>
					</p>
					<p class="m-0"><strong>Appareil : </strong>
						<?php

							$userAgent = $_SERVER['HTTP_USER_AGENT'];

							if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
								echo "Mobile";
							} elseif (preg_match('/ipad|tablet/i', $userAgent)) {
								echo "Tablette";
							} else {
								echo "PC";
							}
						?>
					</p>
					<p class="m-0"><strong>Navigateur : </strong>
						<?php
							if (preg_match('/Edg/i', $userAgent)) {
								echo 'Edge';
							} elseif (preg_match('/Chrome/i', $userAgent)) {
								echo 'Chrome';
							} elseif (preg_match('/Firefox/i', $userAgent)) {
								echo 'Firefox';
							} elseif (preg_match('/Safari/i', $userAgent)) {
								echo 'Safari';
							} else {
								echo 'Inconnu';
							}
						?>
					</p>
					<p class="m-0"><strong>IP : </strong>
						<?php
							echo $_SERVER['REMOTE_ADDR'];
						?>
					</p>

				</article>

				<article id="classement" class="fond p-1 brad-0_5">
					<h2 class="m-0">Classement des victoires</h2>

					<?php 
						$query = "
							SELECT user.login, COUNT(*) AS victoires
							FROM game
							INNER JOIN user ON game.winner = user.id
							WHERE game.winner IS NOT NULL AND user.is_deleted = 0
							GROUP BY game.winner
							ORDER BY victoires DESC
							LIMIT 10
						";

						$result = mysqli_query($mysqli, $query);

						$row = mysqli_fetch_row($result);
						$rank = 1;

						while ($row) {
							$login = $row[0];
							$victoires = $row[1];

							echo "<p class='m-0'><strong>$rank. </strong><span id='rankPos$rank'>$login ($victoires victoires)</span></p>";

							$row = mysqli_fetch_row($result);
							$rank++;
						}
					?>
				</article>
			</div>
		</section>




	</main>

</body>

</html>