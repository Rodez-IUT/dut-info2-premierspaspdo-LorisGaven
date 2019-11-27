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
								<option value="2">Active account</option>
								<option value="1">Waiting for account validation</option>
								<option value="3">Waiting for account deletion</option>
							</select>
			<button type="submit">OK</button>
		</form>
		<table>
			<tr>
				<th>Id</th>
				<th>Username</th>
				<th>Email</th>
				<th>Status</th>
				<th></th>
			</tr>
			<?php
				if (isset($_GET['action'])) {
					$maj = "INSERT INTO action_log (action_date, action_name, user_id)
							VALUES (NOW(), ?, ?)";
					
					try {
					$pdo->beginTransaction();
					$stmt = $pdo->prepare($maj);
					$stmt->execute([$_GET['action'], $_GET['user_id']]);
					
					/*throw ERRMODE_EXCEPTION;*/
					
					$delete = "UPDATE users
							   SET status_id = ?
							   WHERE id = ?";
					
					$stmt = $pdo->prepare($delete);
					$stmt->execute([$_GET['status_id'], $_GET['user_id']]);
					$pdo->commit();
					} catch (Exception $e) {
						$pdo->rollBack();
						throw $e;
					}
				}
			
				$first_letter = "";
				$status = "";
				if (isset($_POST['lettre'])) {
					$first_letter = $_POST['lettre']."%";
				}
				if (isset($_POST['status'])) {
					$status = $_POST['status'];
				}
				$sql = "SELECT users.id as user_id, username, email, status.name
						FROM users JOIN status ON status_id = status.id 
						WHERE username LIKE ? AND status.id = ?
						ORDER BY username";
				
				$stmt = $pdo->prepare($sql);
				$stmt->execute([$first_letter, $status]);
				
				while ($row = $stmt->fetch())
				{
						echo "<tr>";
						echo "<td>".$row['user_id']."</td>";
						echo "<td>".$row['username']."</td>";
						echo "<td>".$row['email']."</td>";
						echo "<td>".$row['name']."</td>";
						if ($row['name'] != 'Waiting for account deletion') {
							echo "<td><a href=\"all_users.php?user_id=".$row['user_id']."&status_id=3&action=askDeletion\">Ask deletion</a></td>";
						}
						echo "</tr>";
				}
			?>
		</table>
	</body>
</html>