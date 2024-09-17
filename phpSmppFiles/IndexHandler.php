<?php

require_once('DbHandler.php');

$result = null;
$dbcontext = (new Db())->getContext();

        
$statement = "
			Select *
			FROM
			    smpp; "; //WHERE date > '2020-04-15 15:50:49';
		try {
			$statement = $dbcontext->query($statement);
			$result = $statement->fetchAll(\PDO::FETCH_ASSOC);
			$res = $result[0]['content'];
		} catch (\PDOException $e) {
		    exit($e->getMessage());
		}