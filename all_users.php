<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>All users</title>
		<style>
			table {
				border-collapse: collapse;
				width: 100%;
			}
			th, td {
				padding: 8px;
				text-align: left;
				border-bottom: 1px solid #ddd;
			}
		</style>
	</head>
	<body>
		<?php
			$host = 'localhost';
			$db   = 'my_activities';
			$user = 'root';
			$pass = 'root';
			$charset = 'utf8mb4';
			$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
			$options = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			];
			try {
				$pdo = new PDO($dsn, $user, $pass, $options);
			} catch (PDOException $e) {
				throw new PDOException($e->getMessage(), (int)$e->getCode());
			}
		?>
		<h1>All users</h1>
		<form action="all_users.php" method="post">
			Start with letter : <input type="text" name="lettre">
			and status is : <select name="status">
								<option value="all">All</option>
								<option value="Active account">Active account</option>
								<option value="Waiting for account validation">Waiting for account validation</option>
								<option value="Waiting for account deletion">Waiting for account deletion</option>
							</select>
			<button type="submit">OK</button>
		</form>
		<table>
			<tr>
				<th>Id</th>
				<th>Username</th>
				<th>Email</th>
				<th>Status</th>
			</tr>
			<?php
				if (isset($_POST['lettre'])) {
					$first_letter = $_POST['lettre'];
				}
				if (isset($_POST['status'])) {
					$status = $_POST['status'];
				}
				$stmt = $pdo->query('SELECT users.id as user_id, username, email, status.name FROM users JOIN status ON status_id = status.id ORDER BY username');
				while ($row = $stmt->fetch())
				{
					if (isset($_POST['lettre']) && isset($_POST['status']) && ($row['name'] == $status || $status == "all")
					&& (substr($row['username'], 0, 1) == $first_letter || $first_letter == "")) {
						echo "<tr>";
						echo "<td>".$row['user_id']."</td>";
						echo "<td>".$row['username']."</td>";
						echo "<td>".$row['email']."</td>";
						echo "<td>".$row['name']."</td>";
						echo "</tr>";
					}
				}
			?>
		</table>
	</body>
</html>