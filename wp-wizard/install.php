<?
$template_testing_folder = ''; //WP Template Path
$site_url = ""; //Static IP for replace path in DB
$wp_zip_path = "wordpress.zip"; //zip file name must be - wordpress.zip

$prodId = $_GET['prodId'];
$is_kava = $_GET['is_kava'];
$is_git_master = $_GET['is_git_master'];
$is_git_package = $_GET['is_git_package'];
$is_version = $_GET['is_version'];
$db_name = $_GET['prodId']; //because it is necessary ;)

$servername = "localhost"; //Server name, host name
$username = "root"; //DB Username
$password = ""; //DB Password
$ROOT = __DIR__;

header('Location:'."$site_url"."$db_name$is_version"."/wp-admin");

function relocateWP(){
	
	global $template_testing_folder, $wp_zip_path, $prodId, $is_version;

	// $source = $wp_zip_path; // Убрать комментарий, если требуется всегда загружать локальный ZIP WordPress.
	$source = "https://wordpress.org/latest.zip";
	
	$dest = "wordpress.zip";
	copy($source, $dest);

	$zip = new ZipArchive;
    $zip->open("$wp_zip_path");
    $zip->extractTo("wordpress");
    $zip->close();

    rename("wordpress/wordpress", "$template_testing_folder"."/"."$prodId"."$is_version");
}

function replace_config(){

global $db_name, $servername, $username, $password, $prodId, $template_testing_folder, $is_version;

$crarset = "utf8mb4";

$filename = "$template_testing_folder"."\\"."$prodId"."$is_version"."\\"."wp-config-sample.php";
$file = file_get_contents($filename);
$file = str_replace('database_name_here', "$db_name$is_version", $file);
$file = str_replace('username_here', "$username", $file);
$file = str_replace('password_here', "$password", $file);
$file = str_replace('utf8', "$crarset", $file);
file_put_contents("$template_testing_folder"."\\"."$prodId"."$is_version"."\\"."wp-config.php", $file);
}
// Creare DB function	
function createDB(){
	global $db_name, $servername, $username, $password, $is_version;
// Create connection
	$conn = new mysqli($servername, $username, $password);
// Check connection
	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	} 

// Create database
	$sql = "CREATE DATABASE `$db_name$is_version`";
	if ($conn->query($sql) === TRUE) {
    echo "WP folder and DB - "."$db_name.$is_version"." - created successfully</br>";
	} else {
    echo "Error creating database: " . $conn->error;
	}

	$conn->close();
}

function is_kava(){
	
	global $is_kava, $template_testing_folder, $prodId, $ROOT, $is_version;

	if($is_kava == 'Yes'){

		$source = "https://github.com/ZemezLab/kava/archive/master.zip";
		$dest = "kava.zip";
		copy($source, $dest);

		$zip = new ZipArchive;
	    $zip->open("kava.zip");
	    $zip->extractTo("kava");
	    $zip->close();

	    rename("kava/kava-master", "$template_testing_folder"."\\"."$prodId"."$is_version"."\\"."wp-content"."\\"."themes"."\\"."kava");
	}
}

function getGITrepositoryMaster(){

	global $is_git_master, $template_testing_folder, $prodId, $ROOT, $is_version;

	if($is_git_master == 'Yes'){

		$_getLink = $_GET['prod_name'];
		$_link_to_git = "http://products.git.devoffice.com/templates/prod-".$prodId; //Это префикс-линка GIT
		$dir = $template_testing_folder;

		$_masterLink = $_link_to_git . "/tree/master";

		$_getMasterZip = $_link_to_git . "/repository/archive.zip?ref=master";


		$source_master = $_getMasterZip;

		$dest_master = "master.zip";

		copy($source_master, $dest_master);

		//unzip MASTER-repo 

		$zip = new ZipArchive;
		$zip->open("master.zip");
		$zip->extractTo($ROOT."//master");
		$zip->close();

		$files = scandir($ROOT."/master", 1);

		$content = file ($ROOT."/"."master/".$files[0]."/style.css");
		foreach ($content as $full_line) {
    		$line = explode (': ', $full_line); 
    		if ($line[0] == "Text Domain") {
        		$result = $line[1];
        	break;
    		}
		}
		$trimmed = rtrim($result);
		rename($ROOT."/"."master/".$files[0], "$template_testing_folder"."\\"."$prodId$is_version"."\\"."wp-content"."\\"."themes"."\\"."$trimmed");

	}

}

function getGITrepositoryPackage(){

	global $is_git_package, $template_testing_folder, $prodId, $ROOT, $is_version;

	if($is_git_package == 'Yes'){

		$_getLink = $_GET['prod_name'];
		$_link_to_git = "http://products.git.devoffice.com/templates/prod-".$prodId; //Это префикс-линка GIT
		$dir    = $template_testing_folder;

		$_packageLink = $_link_to_git . "/tree/package";

		$_getPackageZip = $_link_to_git . "/repository/archive.zip?ref=package";

		$source_package = $_getPackageZip;

		$dest_package = "package.zip";

		copy($source_package, $dest_package);

		//unzip PACKAGE-repo 
		
		$zip = new ZipArchive;
		$zip->open("package.zip");
		$zip->extractTo($ROOT."//package");
		$zip->close();

		$files = scandir($ROOT."/package", 1);
		
		rename($ROOT."/"."package/".$files[0]."/"."theme"."/"."manual_install"."/"."uploads", "$template_testing_folder"."\\"."$prodId$is_version"."\\"."wp-content"."\\"."uploads");

	}
}

function importMainSQL(){

global $db_name, $servername, $username, $password, $site_url, $is_version;

$db_filename = "main.sql";
$file = file_get_contents($db_filename);
$file = str_replace('site_url', "$site_url"."$db_name$is_version", $file);
file_put_contents("main_1.sql", $file);

$db_filename = "main_1.sql";

$connection = mysqli_connect($servername, $username, $password);
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

$db_select = mysqli_select_db($connection, $db_name.$is_version);
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
getGITrepositoryMaster();
getGITrepositoryPackage();
importMainSQL();
replace_config();
