<?php
require_once 'functions.php';
 if(!isset($_SESSION)) 
    { 
        session_start(); 
    }   

function is_empty_val($str){
	if (!empty($_POST[$str])){
		return $_POST[$str];
	}
	return null;
}
$user = "solopov";
$pass = "neto0794";
try {
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);		
	}catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
if (!(empty($_POST['reg']))){
	$prep_q = $pdo -> prepare("SELECT * FROM user WHERE login LIKE ?");
	$login = is_empty_val('login');
	$prep_q -> execute(array($login));
	while ($row = $prep_q->fetch(PDO::FETCH_ASSOC) ){
			foreach($row as $key=>$value){
				if ($row['login'] == $_POST['login']) die("Пользователь с таким именем существует");
			}	
	}
	$prep_q = $pdo -> prepare('INSERT INTO `user` (`login`, `password`) VALUES (?, ?)');
	$login = is_empty_val('login');
	$password = is_empty_val('password');
	if (!(empty($login) && empty($password))){
		$prep_q -> execute(array($login, $password));
	}
	$_SESSION['login']=$login;
	$prep_q = $pdo->prepare("SELECT id FROM user WHERE login=?");
	$prep_q -> execute(array($login));
	$row = $prep_q->fetch(PDO::FETCH_ASSOC);
	$_SESSION['id']=$row['id'];
	header('Location: tasks.php');
}
if (!(empty($_POST['ent']))){
	$prep_q = $pdo->prepare("SELECT * FROM user WHERE login LIKE ? AND password LIKE ?");
	$login = is_empty_val('login');
	$password = is_empty_val('password');
	if (!(empty($login) && empty($password))){
		$prep_q -> execute(array($login, $password));
	} 
	while ($row = $prep_q->fetch(PDO::FETCH_ASSOC) ){
			foreach($row as $key=>$value){
				if ($row['login'] == $_POST['login'] && $row['password'] == $_POST['password']){
						$_SESSION['login']=$login;
						$_SESSION['id']=$row['id'];
						header('Location: tasks.php');
				}  
			}	
	}
	
} 
/*
    if (isPost() && login(getParamPost('login'), getParamPost('password'))) {
       // header('Location: /8/form.php');
        die;
    }
*/
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Авторизация</title>
</head>
<body>
<?php if (isAdmin()): ?>
    Добро пожаловать в административную чайсть сайта.
	 <a href="logout.php">Выход</a>
<?php else: ?>
    <form method="POST">
        <label for="login">Логин</label>
        <input type="text" name="login" id="login">
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password">

        <button name ="ent" type="submit"value="ent">Войти</button>
		<button name ="reg" type="submit" value="reg" >Регистрация</button>
    </form>
<?php endif; ?>
</body>
</html>