<?php

session_start();
if(isset($_SESSION['username'])){
$user=$_SESSION['username'];
echo("Welcome ".$user);
//posts ( id, postedby, follows, datetime, message )
if(isset($_POST['newmessage']))
	{
	 $_SESSION['newmessage']=1;	
	 header('Location:postmessage.php');
	 }
if(isset($_POST['logout']))
	{
	  session_destroy();
      header('Location:board.php');  
	}
if(isset($_GET))
	try{
	$dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
			$dbh = new PDO("sqlite:$dbname");
	$stmt = $dbh->prepare('select * from posts');//order by date and time
	echo('<table border="1px">');
	echo('<tr><td>ID</td><td>postedby</td><td>FullName</td><td>follows</td><td>datetime</td><td>message</td></tr>');
	$stmt->execute();
			while ($row = $stmt->fetch()) {
					
					$query="select fullname from users where username='".$row['postedby']."'";
					$st = $dbh->prepare($query);
					$st->execute();
					$op=$st->fetch();
					echo('<tr>');
					echo('<td>'.$row['id'].'</td>');
					echo('<td>'.$row['postedby'].'</td>');
					echo('<td>'.$op['fullname'].'</td>');
					echo('<td>'.$row['follows'].'</td>');
					echo('<td>'.$row['datetime'].'</td>');
					echo('<td>'.$row['message'].'</td>');
					echo('<td><a href="postmessage.php?folid='.$row['id'].'"><input type="submit" value="REPLY"/></a></td>');
					echo('</tr>');
				}
			
		}catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
					die();
				}
	echo('<table>');
	}
	else{
	header('Location:board.php');
	}
?>
<html>
<body>
<form action="chatroom.php" method="POST">
	<input type="submit" value="Logout" name="logout"/>
	<input type="submit" value="New Message" name="newmessage"/>
</form>
</body>
</html>