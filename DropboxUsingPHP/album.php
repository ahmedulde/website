<!--
Author Name: Ahmed Ulde
URL Address: http://omega.uta.edu/~aau0889/project8/album.php
-->
<fieldset>
<form enctype="multipart/form-data" action="album.php" method="POST">
<input type="hidden" name="upload" value="1" />
Submit this file: <input name="userfile" type="file" /><br/>
<input type="submit"  value="Send File" />
</form>
</fieldset>

<?php
require_once("DropboxClient.php");

error_reporting(E_ALL);
ini_set('display_errors','On');

set_time_limit(0);

// you have to create an app at https://www.dropbox.com/developers/apps and enter details below:
$dropbox = new DropboxClient(array(
	'app_key' => "",      // Put your Dropbox API key here
	'app_secret' => "",   // Put your Dropbox API secret here
	'app_full_access' => false,
),'en');


// first try to load existing access token
$access_token = load_token("access");
if(!empty($access_token)) {
	$dropbox->SetAccessToken($access_token);
	echo "loaded access token:";
	print_r($access_token);
}
elseif(!empty($_GET['auth_callback'])) // are we coming from dropbox's auth page?
{
	// then load our previosly created request token
	$request_token = load_token($_GET['oauth_token']);
	if(empty($request_token)) die('Request token not found!');
	
	// get & store access token, the request token is not needed anymore
	$access_token = $dropbox->GetAccessToken($request_token);	
	store_token($access_token, "access");
	delete_token($_GET['oauth_token']);
}

// checks if access token is required
if(!$dropbox->IsAuthorized())
{
	// redirect user to dropbox auth page
	$return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?auth_callback=1";
	$auth_url = $dropbox->BuildAuthorizeUrl($return_url);
	$request_token = $dropbox->GetRequestToken();
	store_token($request_token, $request_token['t']);
	die("Authentication required. <a href='$auth_url'>Click here.</a>");
}
if(isset($_POST['upload'])){
$thisfile = $_FILES['userfile']['name'];
move_uploaded_file($_FILES['userfile']['tmp_name'],"uploads/" . $thisfile);

$dropbox->UploadFile("uploads/".$thisfile);
echo("Uploaded to drop box!!");
}

echo "<pre>";
echo "<b>Account:</b>\r\n";
print_r($dropbox->GetAccountInfo());


$files = $dropbox->GetFiles("",false);

//if(empty($files)) {
  // $dropbox->UploadFile("leonidas.jpg");
  // $files = $dropbox->GetFiles("",false);
// }

//echo "\r\n\r\n<b>Files:</b>\r\n";
//print_r(array_keys($files));
$file=reset($files);
echo "<ul>";
for($i=0; $i<count($files);$i++)
	{
		echo "<li>";
		echo "<a href='".$dropbox->GetLink($file,false)."' target='window'>".basename($file->path)."</a>";
		echo "<form method='POST' action='album.php'>";
		echo "<input type='hidden' name='delete' value='".basename($file->path)."'><input type='submit' value='delete'></input>";echo "</form>";
		echo "<form method='POST' action='album.php'>";
		echo "<input type='hidden' name='download' value='".basename($file->path)."'><input type='submit' value='download'></input>";
		echo "</form>";
		
		$file=next($files);
		echo "</li>";
	}
	
echo "</ul>";
echo "<iframe name='window'>";
echo"</iframe>";
if(isset($_POST['delete'])){
		$fname=$_POST['delete'];
		$dropbox->Delete($fname);
		header("Location:album.php");//reload page to remove broken links
	}
if(isset($_POST['download'])){
		
		$fname=$_POST['download'];
		$dropbox->DownloadFile($fname,'downloads/'.$fname);
		echo "Image downloaded successfully to downloads folder";
	}
function store_token($token, $name)
{
	if(!file_put_contents("tokens/$name.token", serialize($token)))
		die('<br />Could not store token! <b>Make sure that the directory `tokens` exists and is writable!</b>');
}

function load_token($name)
{
	if(!file_exists("tokens/$name.token")) return null;
	return @unserialize(@file_get_contents("tokens/$name.token"));
}

function delete_token($name)
{
	@unlink("tokens/$name.token");
}



?>
