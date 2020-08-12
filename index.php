<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}s
?>

<?php


// データベース接続
// $dsn="mysql:dbname=tb220230db;host=localhost";
// $user="tb-220230";
// $password="mShhSzhf4L";

$dsn="mysql:dbname=tech;host=localhost";
$user="genki5";
$password="genki5";

$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
// テーブル作成
// $sql = "CREATE TABLE IF NOT EXISTS noticeboard (
//   id INT AUTO_INCREMENT PRIMARY KEY,
//   name VARCHAR(30),
//   comment TEXT,
//   date TIMESTAMP
//  )";
//   $stmt = $pdo->query($sql);
error_reporting(E_ALL & ~E_NOTICE);

$password_page="b";
$name=$_POST["name"];
$comment=$_POST["comment"];
$delete=$_POST["deleteNum"];
$edit=$_POST["edit"];
$date = date("Y年m月d日 H:i:s");
if($_POST["password"]===$password_page){
  // 投稿機能
    if(!empty($_POST["name"]) || !empty($_POST["comment"])){
      if(empty($_POST["editNum"])){
          $sql=$pdo->prepare("INSERT INTO noticeboard (name,comment,date) VALUES('$name','$comment',now())");
          $sql->bindParam(":name",$name,PDO::PARAM_STR);
          $sql->bindParam(":comment",$comment,PDO::PARAM_STR);
          $sql->bindParam(":date",$date,PDO::PARAM_STR);
          $sql->execute();
      }else{
        $editNum=$_POST["editNum"];
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        // $date = new DateTime('now');
        
        $sql = 'update noticeboard set name=:name,comment=:comment where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $editNum, PDO::PARAM_INT);
        // $stmt->bindParam(':date', $date, PDO::PARAM_INT);
        $stmt->execute();
      }
    }


    // 削除機能
    if(!empty($_POST["deleteNum"])){
      $delete=$_POST["deleteNum"];
      $sql="delete from noticeboard where id=:id";
      $stmt=$pdo->prepare($sql);
      $stmt->bindParam(":id",$delete,PDO::PARAM_INT);
      $stmt->execute();
    }
    // 編集機能
    if(!empty($_POST["edit"])){
      $edit=$_POST["edit"];
      $sql="SELECT * FROM noticeboard WHERE id = $edit";
      $stmt=$pdo->query($sql);
      foreach($stmt as $row){
        $editnumber=$row["id"];
        $editname=$row["name"];
        $editcomment=$row["comment"];
    }
  }


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <title>NOTICE BOARD - Tech Base-</title>
</head>
<body>
<div class="container ">
<div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p>
        <a href="password_reset.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
<form action="" method="post">
      Name:<input type="text" name="name" placeholder="Your Name"
      value="<?php if(isset($editname)) {echo $editname;} ?>">
      Comment:<input type="text" name="comment" placeholder="Comment" class="mb-3"
      value="<?php if(isset($editcomment)) {echo $editcomment;} ?>"><br>
      <input  name="editNum" 
      value="<?php if(isset($editnumber)) {echo $editnumber;} ?>" style="display:none;">
      <input type="text" name="password" placeholder="password">
      <input type="submit" name="submit" value="SEND">
    </form>

    <form action="" method="post">
      <input type="text" name="deleteNum" placeholder="Delete">
      <input type="text" name="password" placeholder="password">
      <input type="submit" name="delete" value="DELETE">
    </form>

    <form action="" method="post">
      <input type="text" name="edit" placeholder="Edit">
      <input type="text" name="password" placeholder="password">
      <input type="submit" value="EDIT">
    </form>

    <?php
  $sql="SELECT * FROM noticeboard";
  $stmt=$pdo->query($sql);
  $results = $stmt->fetchAll();
    foreach ($results as $row){
      echo $row['id'].' : ';
      echo $row['name'].'                          ';
      echo $row['comment'].'  ';
      echo "/".$row['date'].'<br>';
      echo "<hr>";
    }
?>
</div>
</body>
</html>