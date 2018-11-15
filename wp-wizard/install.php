<?
$template_testing_folder = ''; //WP Template Path
$site_url = ""; //Static IP for replace path in DB
$wp_zip_path = "wordpress.zip"; //zip file name must be - wordpress.zip

$prodId = $_GET['prodId'];
$is_kava = $_GET['is_kava'];
$db_name = $_GET['prodId']; //because it is necessary ;)

$servername = "localhost"; //Server name, host name
$username = "root"; //DB Username
$password = ""; //DB Password

header('Location:'."$site_url"."$db_name"."/wp-admin");

function relocateWP(){
	
	global $template_testing_folder, $wp_zip_path, $prodId;

	$source = "https://wordpress.org/latest.zip";
	$dest = "wordpress.zip";
	copy($source, $dest);

	$zip = new ZipArchive;
    $zip->open("$wp_zip_path");
    $zip->extractTo("wordpress");
    $zip->close();

    rename("wordpress/wordpress", "$template_testing_folder"."/"."$prodId");
}

function replace_config(){

global $db_name, $servername, $username, $password, $prodId, $template_testing_folder;

$crarset = "utf8mb4";

$filename = "$template_testing_folder"."\\"."$prodId"."\\"."wp-config-sample.php";
$file = file_get_contents($filename);
$file = str_replace('database_name_here', "$db_name", $file);
$file = str_replace('username_here', "$username", $file);
$file = str_replace('password_here', "$password", $file);
$file = str_replace('utf8', "$crarset", $file);
file_put_contents("$template_testing_folder"."\\"."$prodId"."\\"."wp-config.php", $file);
}
// Creare DB function	
function createDB(){
	global $db_name, $servername, $username, $password;
// Create connection
	$conn = new mysqli($servername, $username, $password);
// Check connection
	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	} 

// Create database
	$sql = "CREATE DATABASE `$db_name`";
	if ($conn->query($sql) === TRUE) {
    echo "WP folder and DB - "."$db_name"." - created successfully</br>";
	} else {
    echo "Error creating database: " . $conn->error;
	}

	$conn->close();
}

function is_kava(){
	
	global $is_kava, $template_testing_folder, $prodId;

	if($is_kava == 'Yes'){

		$source = "https://github.com/ZemezLab/kava/archive/master.zip";
		$dest = "kava.zip";
		copy($source, $dest);

		$zip = new ZipArchive;
	    $zip->open("kava.zip");
	    $zip->extractTo("kava");
	    $zip->close();

	    rename("kava/kava-master", "$template_testing_folder"."\\"."$prodId"."\\"."wp-content"."\\"."themes"."\\"."kava");
	}
}

function importMainSQL(){

global $db_name, $servername, $username, $password, $site_url;

$db_filename = "main.sql";
$file = file_get_contents($db_filename);
$file = str_replace('site_url', "$site_url"."$db_name", $file);
file_put_contents("main_1.sql", $file);

$db_filename = "main_1.sql";

$connection = mysqli_connect($servername, $username, $password);
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

$db_select = mysqli_select_db($connection, $db_name);
if (!$db_select) {
    die("Database selection failed: " . mysqli_error($connection));
}

$templine = '';
$lines = file($db_filename);
foreach ($lines as $line){
if (substr($line, 0, 2) == '--' || $line == '')
    continue;

$templine .= $line;
if (substr(trim($line), -1, 1) == ';'){
    mysqli_query($connection, $templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
    $templine = '';
}
}

}

relocateWP();
is_kava();
createDB();
importMainSQL();
replace_config();
