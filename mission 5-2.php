<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<link href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" rel="stylesheet">
</head>
<body style = "background-color:#f8f8f8;">

<?php

session_start();

$editname = "";
$editcomment = "";
$editedpass = "";

// DB接続設定
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS tbtest"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "posttime TEXT,"
. "pass TEXT"
.");";
$stmt = $pdo->query($sql);


//挿入モジュール
if(!empty($_POST["name"])&&!empty($_POST["comment"])&&isset($_POST["insertbutton"])&&empty($_POST["editnumber"])){

if ((isset($_REQUEST["chkno"]) == true) && (isset($_SESSION["chkno"]) == true) && ($_REQUEST["chkno"] == $_SESSION["chkno"])){

$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, posttime, pass) VALUES (:name, :comment, :posttime, :pass)");
$sql -> bindParam(':name', $name, PDO::PARAM_STR);
$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
$sql -> bindParam(':posttime', $posttime, PDO::PARAM_STR);
$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
$name = $_POST["name"];
$comment = $_POST["comment"];
$posttime = date("Y/m/d H:i:s");
$pass = $_POST["pass"];
$sql -> execute();
    
///⑤受信成功メッセージを出力
echo $comment."（送信内容）を受信しました<br><br>";

}
else{
		// 更新・F5ボタンによる再投稿をガード
		echo "更新・F5を押しても、再投稿はされません";
	}

}

//削除モジュール
if(isset($_POST["deletebutton"])){

$delete = $_POST["delete"];
$deletepass = $_POST["deletepass"];

$sql = 'SELECT * FROM tbtest WHERE id=:id ';
//↓差し替えるパラメータを含めて記述したSQLを準備し、
$stmt = $pdo->prepare($sql);
//↓その差し替えるパラメータの値を指定してから、
$stmt->bindParam(':id', $delete, PDO::PARAM_INT);
//↓SQLを実行する。
$stmt->execute();
$results = $stmt->fetchAll();

foreach ($results as $row){
    $subnumber = $row['pass'];
}

if($deletepass == $subnumber && !($subnumber == "")){

//記入例；以下は からで挟まれるPHP領域に記載すること。
//4-1で書いた「// DB接続設定」のコードの下に続けて記載する。
$id = $_POST["delete"];
$sql = 'delete from tbtest where id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

echo $delete."番のコメントを削除します";
}

else{
    ///⑤削除失敗メッセージを出力
    echo "パスワードが違います";
}

}

//編集モジュール１
if(isset($_POST["editbutton"])){

// idがこの値のデータだけを抽出したい、とする
$edit = $_POST["edit"];
$editpass = $_POST["editpass"];

$sql = 'SELECT * FROM tbtest WHERE id=:id ';
//↓差し替えるパラメータを含めて記述したSQLを準備し、
$stmt = $pdo->prepare($sql);
//↓その差し替えるパラメータの値を指定してから、
$stmt->bindParam(':id', $edit, PDO::PARAM_INT);
//↓SQLを実行する。
$stmt->execute();
$results = $stmt->fetchAll();

foreach ($results as $row){
    $subnumber2 = $row['pass'];
}

if($editpass == $subnumber2 && !($subnumber2 == "")){

foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    $editnumber1 = $row['id'];
    $editname = $row['name'];
    $editcomment = $row['comment'];
    $editedpass = $row['pass'];
    }

    echo $editnumber1."番のコメントを編集します";

}
else{
    ///⑤編集失敗メッセージを出力
    echo "パスワードが違います";
}
}

//編集モジュール２
if(isset($_POST["insertbutton"]) && !empty($_POST["editnumber"])){
    if ((isset($_REQUEST["chkno"]) == true) && (isset($_SESSION["chkno"]) == true) && ($_REQUEST["chkno"] == $_SESSION["chkno"])){

    $id = $_POST["editnumber"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $posttime =  date("Y/m/d H:i:s");
    $pass = $_POST["pass"];
    $sql = 'UPDATE tbtest SET name=:name,comment=:comment,posttime=:posttime,pass=:pass WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':posttime', $posttime, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo $id."番のコメントを編集しました";

    }

}

$_SESSION["chkno"] = $chkno = mt_rand();

//↑ここまで入力処理

?>
<br>
<image style="width:25%; padding:10px 20px; float:right; diplay:inline-block;" src="https://thumb.photo-ac.com/2f/2feab464331d982b3b386c1403adf51d_t.jpeg">
<h1 style="padding-right:5px; background-color: lightblue; color: blue; display: inline; width: 150px;"><i class="fas fa-globe-asia" style="padding:0 5px;"></i>住みたい街を募集するスレッド</h1>
<p>あなたの住んでみたい街を上げてください</p>
<p style="color:red;">※投稿時にpassを設定することで、投稿後も削除・編集ができます</p>

<form action="" method="post">
    <input name="chkno" type="hidden" value="<?php echo $chkno; ?>">
    名前：<input type="text" name="name" placeholder="名前" value="<?php echo $editname; ?>">
    <br/>
    コメント：<br/>
    <input type="text" name="comment" size="80" placeholder="コメントを入力してください" rows="4" cols="40" value="<?php echo $editcomment; ?>">
    <br/>
    pass：<input type="text" name="pass" value="<?php echo $editedpass; ?>">
    <br/>
    <input type="submit" name="insertbutton" value="送信">
    <br/>
    
    <hr>

    削除対象番号：<input type="number" min="1" name="delete">
    <br/>
    pass：<input type="text" name="deletepass">
    <br/>
    <input type="submit" name="deletebutton" value="削除">
    <br/>
    
    <hr>
    
    編集対象番号：<input type="number" min="1" name="edit">
    <br/>
    pass：<input type="text" name="editpass">
    <br/>
    <input type="submit" name="editbutton" value="編集">
    <input type="hidden" name="editnumber" value="<?php if(!empty($editnumber1)){echo $editnumber1;}?>">
</form>

<hr>

<?php

//↓ここから出力処理

$sql = 'SELECT * FROM tbtest';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].': ';
    echo '<span style="color: green;">'.$row['name'].'</span>'.' ';
    echo '<span style="color: gray;">'.$row['posttime'].'</span>'.'<br>';
    echo '<span style="font-weight:bold;font-size:24px;line-height:36px;margin-bottom:70px;margin-top:8px;line-height:1.8;">'.$row['comment'].'</span>'.'<br><br>';
}

?>

<div style = "margin-bottom:500px;">
</div>

</body>
</html>