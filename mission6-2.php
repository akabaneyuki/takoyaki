<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
        
        //DB接続設定
        $dsn = 'mysql:dbname=******;host=localhost';    //スペース含まない
        $user = '****';
        $password = '********';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        
        
        //4-2　データベース内にテーブルを作成
         $sql = "CREATE TABLE IF NOT EXISTS tbtest"
        . "("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date char(32),"
        . "pass char(32)"   //  最後だけ、カンマ付けない
        . ");";
        $tsmt = $pdo->query($sql);
        /*  
            テーブル名:tbtest   
            登録できる項目（カラム）は
            id ・自動で登録されているナンバリング。
            name ・名前を入れる。文字列、半角英数で32文字。<- date,passも
            comment ・コメントを入れる。文字列、長めの文章も入る。
        */
        
    	
    	$new_name = $_POST["name"];
    	$new_comment = $_POST["comment"];
    	$com_date = date("Y年m月d日 H時i分s秒");
        $new_pass = $_POST["pass"];
        
        
        
        $edit_pass = $_POST["edit_pass"];
        $edit_num = $_POST["edit_num"];
        $id = $edit_num;
        //編集(番号探索)
        if(!empty($edit_num) && !empty($_POST["submit3"]) && !empty($edit_pass) && $edit_pass != "パスワード"){
            $sql = 'SELECT * FROM tbtest WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
            	foreach ($results as $row){
            		//$rowの中にはテーブルのカラム名が入る
            		$pass = $row['pass'];   //保持してあるpass取り出し
            		if($pass == $edit_pass){    //保持してあるpassと入力したpassは同一か
                		$edit_num = $row['id'];     //passが同一なら代入
                		$edit_name = $row['name'];
                		$edit_comment = $row['comment'];
            		}
            	}
        }
    	
    	
    	//編集（内容書き換え)
    	$editnum = $_POST["editnum"];
    	if(!empty($new_name) && !empty($new_comment) && !empty($_POST["submit1"]) && !empty($editnum) && !empty($new_pass) 
    	&& $new_name != "名前" && $new_comment != "コメント" && $new_pass != "パスワード"){
        	//4-7　選択して内容を変更
        	$id = $editnum; //変更する投稿番号
        	$sql = 'SELECT * FROM tbtest WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
            	foreach ($results as $row){
            		//$rowの中にはテーブルのカラム名が入る
            		$pass = $row['pass'];       //指定したid（番号の）passを取り出す
            	}
            if($pass == $new_pass){ //保持してあるパスワードと、入力したパスワードが同一か
                $name = $new_name;  //  同一の時、書き換える
            	$comment = $new_comment; 
            	$date = $com_date;
            	$sql = 'UPDATE tbtest SET name=:name,comment=:comment, date=:date, pass=:pass WHERE id=:id';
            	$stmt = $pdo->prepare($sql);
            	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
            	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            	$stmt->bindParam(':date', $date, PDO::PARAM_STR);
            	$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);   //省略可能？
                $stmt->execute();
            }
    	
    	    
    	    
    	    
    	}else{
        	
        	
        	
        	//新規投稿 
        	if(!empty($new_name) && !empty($new_comment) && !empty($_POST["submit1"]) && $new_name != "名前" && $new_comment != "コメント" 
        	&& !empty($new_pass) && $new_pass != "パスワード"){
            	//4-5　投稿（データ入力）
            	$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
            	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
            	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            	$name = $new_name;
            	$comment = $new_comment; 
            	$date = $com_date;
            	$pass = $new_pass;
            	$sql -> execute();
                //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなる。最適なものを適宜決めよう。
        	}
        	
        	
            
            $delete_num = $_POST["delete_num"];
            $delete_pass = $_POST["delete_pass"];
        	//削除 
        	if(!empty($delete_num) && !empty($_POST["submit2"]) && !empty($delete_pass) && $delete_pass != "パスワード"){
        	    $id = $delete_num;        //特定できるなら番号、名前でも良いみたい。ただし、該当するもの全部消える
            	$sql = 'SELECT * FROM tbtest WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // ←SQLを実行する。
                $results = $stmt->fetchAll(); 
                	foreach ($results as $row){
                		$pass = $row['pass'];       //指定したid（番号の）passを取り出す
                	}
                if($pass == $delete_pass){  //指定したid（番号）のpassと入力したpassは同一か
                	//4-8   削除
                	$sql = 'delete from tbtest where id=:id';   //同一なら削除
                	$stmt = $pdo->prepare($sql);
                	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
                	$stmt->execute();
                }
        	}
    	
    	
    	
    	}
    ?>

     <form action = "mission_5-1.php" method = "post">
        [新規投稿]<br>
        <input type = "text" name = "name" value = "<?php if(!empty($edit_name)){echo $edit_name;}else{echo "名前";} ?>">
        <input type = "text" name = "comment" value = "<?php if(!empty($edit_name)){echo $edit_comment;}else{echo "コメント";} ?>">
        <input type = "text" name = "pass" value = "パスワード">
        <input type = "hidden" name = "editnum" value = "<?php if(!empty($edit_name)){echo $edit_num;} ?>">
        <input type = "submit" name = "submit1">
        <br>[削除]<br>
        <input type = "number" name = "delete_num" placeholder = "削除番号">
        <input type = "text" name = "delete_pass" value = "パスワード">
        <input type = "submit" name = "submit2" value = "削除">
        <br>[編集]<br>
        <input type = "number" name = "edit_num" placeholder = "編集番号">
        <input type = "text" name = "edit_pass" value = "パスワード">
        <input type = "submit" name = "submit3" value = "編集">
    </form>
    <br>_______________________________________________________________________________<br><br>
    
    
    
    <?php
    	
    	//4-6 表示
    	//$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる必要がある
    	$sql = 'SELECT * FROM tbtest';
    	$stmt = $pdo->query($sql);
    	$results = $stmt->fetchAll();
    	foreach ($results as $row){
    		//$rowの中にはテーブルのカラム名が入る
    		echo $row['id'].',';
    		echo $row['name'].',';
    		echo $row['comment'].',';
    		echo $row['date'].',<br>';
    	echo "<hr>";
    	}
    	
    	
    	
    	
        
        /*
        4-3, 4-4, 4-9は未使用
        4-6はpassの確認に利用した
    
    	//4-6　選択して表示
    	//抽出したいとき　WHEREを使う
    	echo "<br><br>番号1を選択<br>";
    		$id = 1 ; // idがこの値のデータだけを抽出したい、とする
        
        $sql = 'SELECT * FROM tbtest WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        	foreach ($results as $row){
        		//$rowの中にはテーブルのカラム名が入る
        		echo $row['id'].',';
        		echo $row['name'].',';
        		echo $row['comment'].'<br>';
            	echo "<hr>";
        	}
	    */
	    
    ?>
</body>
</html>
