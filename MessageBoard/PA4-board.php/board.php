<!--
Student Name: Ahmed Ulde
URL Address: http://omega.uta.edu/~aau0889/project4/board.php
-->

<html>
<head><title>Message Board</title></head>
<?php
if(isset($_POST['registerbtn']))
{
header("Location:register.php");
}

if(isset($_POST['loginbtn']))
{  
	$username=$_POST['uname'];
	$password=$_POST['upass'];
	$phash=md5($password);//hash and then compare.
	//echo($username."=>".$password);
	try
	{
		$dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
		$dbh = new PDO("sqlite:$dbname");
		$query=$dbh->prepare("SELECT * from users where username='".$username."' and password='".$phash."'");//check with uname and password
		$query->execute();
		//print_r($query);
	    $row = $query->fetch();
		if($row!=null) {
					print_r($row);
					session_start();
					$_SESSION['username']=$username;
					header("Location:chatroom.php");
				}
		else{
				echo "<script type='text/javascript'>alert('Invalid username and password combination');</script>";
				//header("Location:board.php");
				}
	}catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
}
?>
<style>
div{
margin-top:150px;
background-color:#c0c0c0;
}
body{background-color:#eee;}
</style>
<body>
<p>
You cannot goto chatroom.php or postmessage.php unless you login. Without Login you can goto board.php or register.php. For testing you can user the username as smith and password as mypass.
</p>
<div align="center">
	<form action="board.php" method="POST">
	User Name:<input type="text" name="uname"></input><br/>
    Password:<input type="password" name="upass"></input><br/>
	<input id="logbtn" type="submit" name="loginbtn" value="Login"></input>
	<input type="submit" name="registerbtn" value="New Users Must Register Here"></input>
	</form>
</div>

</body>
</html>
