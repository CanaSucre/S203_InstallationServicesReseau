#!/usr/bin/php

<?php
	$mysqli = mysqli_connect("localhost","iut_s203","mdp","s203");

	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
	}

	$query = "DELETE FROM user WHERE isVerified = 0";
	mysqli_query($mysqli, $query);

?>
