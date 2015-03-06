<?php

	if(isset($_POST['register']))
	{
		$fullname=$_POST['fullname'];
		$username=$_POST['username'];
		$password=$_POST['password'];
		$email=$_POST['email'];

		try {
				$dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
				$dbh = new PDO("sqlite:$dbname");
				$dbh->beginTransaction();
				//check if user is not present
				$stmt = $dbh->prepare('select * from users where fullname="'.$fullname.'" or username="'.$username.'"');
				$stmt->execute();
				if($row = $stmt->fetch()) 
				{
				echo('<p><font color="red">'.$row['fullname']." Already Exists. Please try with new fullname..".'</font></p>');
				echo('<p><font color="red">'.$row['username']." Already Exists. Please try with new username..".'</font></p>');
				}
				//insert if new user
				else
				$dbh->exec('insert into users values("'.$username.'","' . md5($password) . '","'.$fullname.'","'.$email.'")')
				or die(print_r($dbh->errorInfo(), true));
				$dbh->commit();

				
			} catch (PDOException $e) {
						print "Error!: " . $e->getMessage() . "<br/>";
						die();
					}		

	}

	if(isset($_POST['back'])){
	header('Location:board.php');
	}
?>

<html>
<body>
	<form action="register.php" method="POST">
	    fullname:<input type="text" name="fullname"/>
		username:<input type="text" name="username"/>
		password:<input type="password" name="password"/>
		email:<input type="email" name="email"/>
		<input type="submit" name="register" value="REGISTER"/>
		<input type="submit" name="back" value="<<GO BACK"/>
	</form>
</body>
</html>