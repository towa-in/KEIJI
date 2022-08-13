<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>KEIJI</title>
        <style type="text/css">
             .button {
               display       : inline-block;
               border-radius : 5%;          
               font-size     : 11pt;       
               text-align    : center;      
               cursor        : pointer;     
               padding       : 2px 4px; 
               background    : #00acee;    
               color         : #ffffff;    
               line-height   : 1em;        
               transition    : .3s;        
               border        : 2px solid #00acee;    
             }
             .button:hover {
               color         : #00acee;     
               background    : #ffffff;   
             }
        </style>
    </head>
    <body style="background-color:#EFEFEF;">
        <div style="background-color:white">
        <h1>KEIJI-BAN</h1>
        </div>
        <a href="main-v1.php" class="button">日本語</a>
        <a href="https://chrome.google.com/webstore/detail/google-translate/aapbdbdomjkkjkaonfhkkikfgjllcleb?hl=ja" class="button">English</a>
        <h2 style="color:red;">テストページ</h2>
        
        <?php
          error_reporting(~E_NOTICE);
          
          // DB接続設定
          $dsn = 'mysql:dbname=********db;host=localhost';
          $user = 'ユーザー名';
          $password = 'パスワード';
          $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
          
          // テーブル作成
          $sql = "CREATE TABLE IF NOT EXISTS mission511"
          ." ("
          . "id INT AUTO_INCREMENT PRIMARY KEY,"
          . "name char(32),"
          . "comment TEXT,"
          . "date TEXT,"
          . "pword char(32)"
          .");";
          $stmt = $pdo->query($sql);
          

          $dt = date("Y/m/d H:i:s");  // 投稿編集日時

          $na = $_POST["name"];  // 投稿フォームに入力された名前
          $com = $_POST["com"];  // 投稿フォームに入力されたコメント
          $editnum = $_POST["editnum"];  // 編集用の編集行数
          $postpass = $_POST["postpass"];  // 投稿フォームに入力されたパスワード

          $delete = $_POST["delete"];  // 削除フォームに入力された行番号
          $delpass = $_POST["delpass"];  // 削除フォームに入力されたパスワード

          $edit = $_POST["edit"];  // 編集フォームに入力された行番号
          $editpass = $_POST["editpass"];  // 編集フォームに入力されたパスワード
          

          if ($com == null){  //コメントが空なら何もしない
          } else {

              // 新規投稿の場合
              if ($editnum == null){  // 編集行数が設定されていない新規投稿の場合
                  if ($na == null){  // 名前が設定されていない場合、名無しに設定
                      if ($postpass != null){
                          $sql = $pdo -> prepare("INSERT INTO mission511 (name, comment, date, pword) VALUES (:name, :comment, :date, :pword)");
                          $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                          $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                          $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                          $sql -> bindParam(':pword', $pword, PDO::PARAM_STR);
                          $name = "名無し";
                          $comment = $com;
                          $date = $dt;
                          $pword = $postpass;
                          $sql -> execute();
                          echo "<h4>投稿しました</h4><br>";
                      } else {
                          echo "<h4>投稿エラー：パスワードを入力してください</h4><br>";
                      }
                    
                  } else {  // 名前が入力されていればそのままデータベースに
                       if ($postpass != null){
                          $sql = $pdo -> prepare("INSERT INTO mission511 (name, comment, date, pword) VALUES (:name, :comment, :date, :pword)");
                          $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                          $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                          $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                          $sql -> bindParam(':pword', $pword, PDO::PARAM_STR);
                          $name = $na;
                          $comment = $com;
                          $date = $dt;
                          $pword = $postpass;
                          $sql -> execute();
                          echo "<h4>投稿しました</h4><br>";
                       } else {
                          echo "<h4>投稿エラー：パスワードを入力してください</h4><br>";
                       }
                  }

              // 再投稿（編集）の場合
              } else {  
                  if ($postpass != null){
                     $id = $editnum;  // 編集する投稿番号
                     $name = $na;
                     $comment = $com; 
                     $pword = $postpass;
                     $date = $dt;
                     $sql = 'UPDATE mission511 SET name=:name,comment=:comment,date=:date,pword=:pword WHERE id=:id';
                     $stmt = $pdo->prepare($sql);
                     $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                     $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                     $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                     $stmt->bindParam(':pword', $pword, PDO::PARAM_STR);
                     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                     $stmt->execute();
                     echo "<h4>編集しました</h4><br>";
                  } else {
                      echo "<h4>編集エラー：パスワードを入力してください</h4><br>";
                  }
              }
          }
          

          // 削除フォーム処理
          if (is_numeric($delete)){
              $id = $delete;  // 削除する投稿番号
              $sql = 'SELECT * FROM mission511 WHERE id=:id ';
              $stmt = $pdo->prepare($sql);                  
              $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
              $stmt->execute();                          
              $results = $stmt->fetchAll();
              
              foreach ($results as $row){
                  if ($row['pword'] == $delpass){  // 入力されたパスワードの確認
                      $id = $delete;
                      $sql = 'delete from mission511 where id=:id';
                      $stmt = $pdo->prepare($sql);
                      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                      $stmt->execute();
                      echo "<h4>削除しました</h4><br>";
                  } else {
                      echo "<h4>削除エラー：パスワードが違います $date</h4><br>";
                  }
              }     
                  
          
          // 編集フォーム処理
          if (is_numeric($edit)){
              $id = $edit;  // 編集する投稿番号
              $sql = 'SELECT * FROM mission511 WHERE id=:id ';
              $stmt = $pdo->prepare($sql);                 
              $stmt->bindParam(':id', $id, PDO::PARAM_INT);
              $stmt->execute();                       
              $results = $stmt->fetchAll();
              
              foreach ($results as $row){
                  if ($editpass == $row['pword']){  // 入力されたパスワードの確認
                     $elnum = $edit;
                     $elname = $row['name'];
                     $elcom = $row['comment'];
                     echo "<h4>編集を受け付けました<br>フォームに編集内容と新しいパスワードを入力して下さい</h4><br>";
                  } else {
                      echo "<h4>編集エラー：パスワードが違います </h4><br>";
                  }
              }
          
          
          // コメント表示
          $sql = 'SELECT * FROM mission511';
          $stmt = $pdo->query($sql);
          $results = $stmt->fetchAll();
              
          foreach ($results as $row){
              echo "<h3>".$row['id'].' ';
              echo "名前：<font color='green'>".$row['name']." </font>";
              echo $row['date'].'</h3>';
              echo $row['comment']."<br><br>";
          }

          
        ?>
        <br><hr><br>
        書き込む<br>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前(任意)" value=<?php if(isset($elname)){echo $elname;}?>>
            <input type="hidden" name="editnum" placeholder="編集行数" value=<?php if(isset($elnum)){echo $elnum;}?>>
            <input type="password" name="postpass" placeholder="パスワードを設定(必須)"><br>
             <input type="text" name="com" placeholder="コメント(必須)" style="width:339px;" value=<?php if(isset($elcom)){echo $elcom;}?>>
            <input type="submit" name="submit" class="button">
            
            <br><br>
            投稿の削除、編集<br>
            <input type="number" name="delete" placeholder="削除したい投稿番号">
            <input type="password" name="delpass" placeholder="投稿時のパスワード">
            <input type="submit" name="submit" value="削除" class="button">
            <br>
            
            <input type="number" name="edit" placeholder="編集したい投稿番号">
            <input type="password" name="editpass" placeholder="投稿時のパスワード">
            <input type="submit" name="submit" value="編集" class="button"><br>
            <br><br>
        </form>
    </body>
    </div>
</html>
