<?php
 if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

function is_empty_val($str){
	if (!empty($_POST[$str])){return $_POST[$str];
	}
	return null;
}
$user = "solopov";
$pass = "neto0794";
$change_on = false;
$tmp = '';
$filter_on = false;
try {
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);//+	
	}catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
if (!(empty($_POST['insert']))){
	$prep_q = $pdo -> prepare('INSERT INTO task(user_id,assigned_user_id, description, is_done, date_added) VALUES (?,?,?,?,?)');	
	$newtask = is_empty_val('newtask');	
	$prep_q -> execute(array($_SESSION['id'], $_SESSION['id'], $newtask, 1 ,date('Y-m-d H:i:s')));
}	
if (!(empty($_POST['delete']))){
	$prep_q = $pdo->prepare('DELETE FROM task WHERE id=?');
	$numtask = is_empty_val('delete');
	$prep_q -> execute(array($numtask));
	}	
if (!(empty($_POST['onchange']))){
	$change_on=true;
	$tmp=$_POST['onchange'];
}	
if (!(empty($_POST['ready']))){
	$prep_q = $pdo->prepare('UPDATE task SET description=? WHERE id=?');
	$changedtask=is_empty_val('changetask');
	if (!(empty($changedtask))){
		$id=$_POST['ready'];
		$prep_q->execute(array($changedtask,$id));
	}
}
if (!(empty($_POST['execute']))){
	$prep_q = $pdo->prepare('UPDATE task SET is_done=0 WHERE id=?');	
	$id=$_POST['execute'];
	$prep_q->execute(array($id));
}
if (!(empty($_POST['givetouser']))){
	$prep_q = $pdo->prepare('UPDATE task SET assigned_user_id = ? WHERE id = ?');
	$index='sel_user_'.$_POST['givetouser'];	
	$assigned_user_id=$_POST[$index];
	$id = $_POST['givetouser'];
	$prep_q->execute(array($assigned_user_id, $id ));
}
try {	
	$col_name = $pdo -> query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'task' AND table_schema = 'solopov'");//+
	if (!(empty($_POST['sortbydate']))){
		$data_task = $pdo->query("SELECT * FROM task ORDER BY date_added");//data
	}
	elseif (!(empty($_POST['sortbydescription']))){
		$data_task = $pdo->query("SELECT * FROM task ORDER BY description");//data
	}
	elseif(!(empty($_POST['sortbystatus']))){
		$data_task = $pdo->query("SELECT * FROM task ORDER BY is_done");//data
	}
	else{	
		$col_name_user = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'user' AND table_schema = 'solopov'");//+	
		$data_user =	$pdo->query("SELECT * FROM user");		
		
		$col_name_task = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'task' AND table_schema = 'solopov'");//+	
		
	}
	}catch (PDOException $e){
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
header("Refresh");	
echo"<p> Здраствуйте, " . $_SESSION['login'] . "</p>";
echo "<a href='logout.php'>Выход</a>";
//echo date('Y-m-d H:i:s');
?>



<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <style>
	table {
    border-collapse: collapse;
	
	}
	th{
	background: gray;
	}
	td, th{
    border: 1px solid black;
	}
</style>  
</head>
<body>
<form action="tasks.php" method="post">
	<input name="newtask" type="text" placeholder="Новая задача">	
	<button type="submit" name="insert" value="insert">Добавить</button>
	Сортировать по:
	<button type="submit" name="sortbydate" value="sortbydate">дате</button>
	<button type="submit" name="sortbydescription" value="sortbydescription">описанию</button>
	<button type="submit" name="sortbystatus" value="sortbystatus">статусу</button>
	<table>	
		<tr>
			<th>Автор</th>
			<th>Описание задачи</th>
			<th>Состояние</th>
			<th>Дата добавления</th>
			<th>Ответственный</th>
		</tr>
		<?php
			$data_task = $pdo->prepare('SELECT user.id as idfromuser, task.id, user_id, description, is_done, date_added,assigned_user_id, login FROM `task` INNER JOIN user ON task.user_id = user.id WHERE user_id=?');
			$var = $_SESSION['id'];
			$data_task->execute(array($var));
			while ($row=$data_task->fetch(PDO::FETCH_ASSOC) ){ 
		?>
		<tr>
			<?php //подсветка
				foreach($row as $key=>$value){ 
					if (($key=='idfromuser')){
						continue;
					}
					if (($key=='id')){
						continue;
					}
					if (($key=='login')){
						continue;
					}
					if (($key=='is_done')){
						if (!(empty($row[$key]))){ ?>
							<td> В процессе </td>
							<?php continue; } else { ?>
							<td style="background-color: LightGreen  "> Завершено </td>
							<?php continue; } ?>
					<?php }; ?>					
					<?php
					if (($key=='user_id')){
						if (!(empty($row[$key]))){ ?>
							<td> <?php echo $row['login'] ?> </td>
							<?php continue;
						}; 
					}
				 ?>		
				 <?php 
				 if (($key=='assigned_user_id')){	
					$data =	$pdo->prepare("SELECT login FROM user WHERE id = ?");
					$data->execute(array($row[$key]));
					$assign_usr = $data -> fetch(PDO::FETCH_ASSOC);?>
					<td> <?php  echo $assign_usr['login']; ?> </td>
					<?php continue;
				}; ?>
			 	<td> <?php echo strip_tags($value) . "</br>"; ?></td>							
			<?php } ?>	
				<td style="border-style: none">
				<select name="sel_user_<?php echo $row['id']; ?>">
				<?php
					$data_user_sel = $pdo->query("SELECT id,login FROM user");
					while ($usr_sel=$data_user_sel->fetch(PDO::FETCH_ASSOC) ){
				?>
					<option value="<?php echo $usr_sel['id']; ?>" ><?php echo strip_tags($usr_sel['login']); ?></option>
					<?php }; ?>	
					</select>
					<button type="submit" name="givetouser" value="<?php echo $row['id']; ?>">Передать</button>
					<button type="submit" name="onchange" value="<?php echo $row['id']; ?>">Изменить</button>
					<button type="submit" name="delete" value="<?php echo $row['id']; ?>">Удалить</button>
					<?php
						if ($row['assigned_user_id']==$_SESSION['id']){
								$hide_btn=false;
							}else{
								$hide_btn=true;
							}
						if($hide_btn==false){ ?>
							<button type="submit" name="execute" value="<?php echo $row['id']; ?>">Выполнить</button>
						<?php } ?>
				
					<?php
						if ( $change_on && $tmp==$row['id']) {?>
							<input name="changetask" type="text" placeholder="Изменить задачу">
							<button type="submit" name="ready" value="<?php echo $row['id']; ?>">Готово</button>
					<?php } ?>

				</td>
				</tr>			
		<?php }; ?>			
	</table>
	<p>Задания от других пользователей<p>
	<table>
	<tr>
		<th> Описание </th>
		<th> Состояние</th>
		<th> Дата добавления </th>
		<th> Автор </th>
	</tr>
	<?php
		$a = $pdo->prepare('SELECT assigned_user_id, task.id, description, is_done, date_added, login FROM `task` INNER JOIN user ON task.user_id = user.id WHERE  task.assigned_user_id!=task.user_id AND task.assigned_user_id = ?');
		$var = $_SESSION['id'];
		$a->execute(array($var));
		while ($row=$a->fetch(PDO::FETCH_ASSOC) ){ 
	?>	
		<tr>
		<?php 
			foreach($row as $key=>$value){ 
				if (($key=='assigned_user_id')){
					continue;
				}
				if (($key=='id')){
					continue;
				}
				if (($key=='is_done')){
					if (!(empty($row[$key]))){ ?>
						<td> В процессе </td>
						<?php continue; } else { ?>
						<td style="background-color: LightGreen  "> Завершено </td>
						<?php continue; } ?>
				<?php }; ?>
				<td> <?php echo strip_tags($value) ."</br>"; ?></td>	
		<?php }; ?>
				<td style="border-style: none">
				<?php
					if ($row['assigned_user_id']==$_SESSION['id']){
						$hide_btn=false;
					}else{
						$hide_btn=true;
					}
					if($hide_btn==false){ ?>
						<button type="submit" name="execute" value="<?php echo $row['id']; ?>">Выполнить</button>
					<?php } ?>
				</td>
		<?php }; ?>
		</tr>	
	
	</table>
</form>
</body>
</html>
