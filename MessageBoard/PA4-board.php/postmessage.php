<?php
//posts ( id, postedby, follows, datetime, message )
session_start();
if(isset($_SESSION['username'])){
$user=$_SESSION['username'];

if(isset($_SESSION['newmessage'])){
//echo("new message is set");
	$_SESSION['folid']=0;//put follows as 0 in database since it is nt following any message that is new message.
	unset($_SESSION['newmessage']);
}

if(isset($_GET['folid'])){
//echo("get method is set");
$_SESSION['folid']=$_GET['folid'];
unset($_GET);
}	

if(isset($_POST['reply'])){
		$msg=$_POST['tarea'];
		$id=uniqid();//generates unique ID
		$d=strtotime("-7 Hours");//subtract 7 hrs from date function
		$dt=date("Y-m-d h:i:sa",$d);//returns date and time
		$follows=$_SESSION['folid'];
		try{
			$dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
			$dbh = new PDO("sqlite:$dbname");
			$statement="INSERT INTO posts values('".$id."','".$user."','".$follows."','".$dt."','".$msg."')";
			echo($statement);
			$dbh->exec($statement)or die(print_r($dbh->errorInfo(), true));

		}catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
					die();
				}

		header('Location:chatroom.php');
	}
	}else{header('Location:board.php');}//if u are not logged in u will be redirected to login for security
?>
<html>
	<body>
		
		<form action="postmessage.php" method="post">
		<textarea rows="20" cols="100" name="tarea">
		
		</textarea><br/>
		<input type="submit" name="reply" value="POST YOUR MESSAGE">
		</form>
	</body>
</html>