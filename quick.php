<?php include ('connection.php');
//ini_set('max_execution_time', 300);
$platform = 'ios';
$linesCountios = 0;
$linesCountand = 0;
$last_id_ios = 0;
$last_id_and = 0;
$sql="INSERT into link_test_ios(affiliate,campaign,channel,offer_id,country,link) values(?,?,?,?,?,?)";
$sql1="INSERT into link_test(affiliate,campaign,channel,offer_id,country,link) values(?,?,?,?,?,?)";
$io="INSERT into users_sheet_info(start_no,end_no,platform) values(?,?,?)";
$and="INSERT into users_sheet_info(start_no,end_no,platform) values(?,?,?)";
if(isset($_POST['submit'])){
if($_FILES['csv_info']['name']){
$arrFileName = explode('.',$_FILES['csv_info']['name']);
if($arrFileName[1] == 'csv'){
$handle = fopen($_FILES['csv_info']['tmp_name'], "r");
fgetcsv($handle);
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
{
    $version =substr($data[1], -3);
if ($version==$platform)
{
    $linesCountios ++;
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)) 
    {
        echo "Unable to insert data in ios";
    }
    else 
    {
        mysqli_stmt_bind_param($stmt,"ssssss",$data[0],$data[1],$data[2],$data[3],$data[4],$data[5]);
        mysqli_stmt_execute($stmt);
    }
    $last_id_ios =mysqli_insert_id($conn);
    //echo $last_id_ios;
}
else 
{
    $linesCountand ++;
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql1)) 
    {
        echo "unable to insert data in Android ";
    }
    else 
    {

        mysqli_stmt_bind_param($stmt,"ssssss",$data[0],$data[1],$data[2],$data[3],$data[4],$data[5]);
        mysqli_stmt_execute($stmt);
    }
    $last_id_and =mysqli_insert_id($conn);

}
}
$frst_and = $last_id_and - $linesCountand;
$frst_ios = $last_id_ios - $linesCountios;
if ($linesCountios != 0)
{
    if(mysqli_stmt_prepare($stmt,$io))  
    {
        mysqli_stmt_bind_param($stmt,"iis",$frst_ios,$last_id_ios,$platform);
        mysqli_stmt_execute($stmt);
    }
}
else
{
    echo "no record inserted in ios ";
    echo "<br>";
}
if ($linesCountand != 0)
{
    $plat ='android';
    if(mysqli_stmt_prepare($stmt,$and))  
    {
        mysqli_stmt_bind_param($stmt,"iis",$frst_and,$last_id_and,$plat);
        mysqli_stmt_execute($stmt);
    }
}
else 
{

    echo "no record inserted in android ";
    echo "<br>";
}
fclose($handle);
print "Import done sucessfully ";
}
}
}
