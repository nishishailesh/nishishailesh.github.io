<?php
//$GLOBALS['nojunk']='';
require_once 'base/verify_login.php';
require_once 'single_table_edit_common.php';
	////////User code below/////////////////////
echo '		  <link rel="stylesheet" href="project_common.css">
		  <script src="project_common.js"></script>';	
$link=get_link($GLOBALS['main_user'],$GLOBALS['main_pass']);
$user=get_user_info($link,$_SESSION['login']);
//$auth=explode(',',$user['authorization']);
//print_r($auth);
if(isset($_POST['date']))
{
	$show_date=$_POST['date'];	
}
else
{
	$show_date=date("Y-m-d");
}

list_available_tables($link);

manage_stf($link,$_POST);

	$sql='
(	
select 
	i.id,i.date,f.food_item,i.gm,
	round((f.calories*i.gm)/f.weight,0) as Calories,
	round((f.calories*i.gm)/f.weight,0) as Calories,
	round((f.protein*i.gm)/f.weight,1) as Protein,
	round((f.potassium*i.gm)/f.weight,0) as Potassium,
	round((f.phosphorus*i.gm)/f.weight,0) as Phosphorus,
	round((f.calcium*i.gm)/f.weight,0) as Calcium,
	round((f.sodium*i.gm)/f.weight,0) as Sodium,
	round((f.fat*i.gm)/f.weight,1) as Fat,
	round((f.carbohydrate*i.gm)/f.weight,1) as Carbohydrate,
	round((f.fiber*i.gm)/f.weight,1) as Fiber,
	i.remark
	from myfood f, intake i
			where 
					date=\''.$show_date.'\'
					and
					f.id=i.food_id
					
)					
	union				
(					
select 
	"Grand","Total","For","'.$show_date.'",
	round(sum((f.calories*i.gm)/f.weight) ,0) as  Calories,
	round(sum((f.calories*i.gm)/f.weight) ,0) as  Calories,
	round(sum((f.protein*i.gm)/f.weight) ,1) as  Protein,
	round(sum((f.potassium*i.gm)/f.weight) ,0) as Potassium,
	round(sum((f.phosphorus*i.gm)/f.weight) ,0) as  Phosphorus,
	round(sum((f.calcium*i.gm)/f.weight) ,0) as  Calcium,
	round(sum((f.sodium*i.gm)/f.weight) ,0) as  Sodium,
	round(sum((f.fat*i.gm)/f.weight) ,1) as  Fat,
	round(sum((f.carbohydrate*i.gm)/f.weight) ,1) as  Carbohydrate,
	round(sum((f.fiber*i.gm)/f.weight) ,1) as  Fiber,
	""
	from myfood f, intake i
			where 
					date=\''.$show_date.'\'
					and
					f.id=i.food_id					
					
					
					
)					';
	//echo $sql;
	echo '<h4 class="bg-info">Food intake on '.$show_date.'</h4>';
	view_sql_result_as_table($link,$sql,$show_hide='no');

echo '
<h5 class="bg-warning" data-toggle="collapse" data-target="#help" >Help</h5>
<ul id="help" class="collapse">
	<li><b>Add Blank:</b> Add new empty record. </li>
	<li>Click pencil icon <img width=30 src=img/edit.png>to edit blank record / existing record </li>
	<li>Click BIN icon <img width=30 src=img/delete.png> to delete  it</li>
	<li><b>search:</b> Find existing records.</li>
	<li><b>list:</b> List records.(Max 100)</li>
	<li><b>How do I edit old record?:</b> Click list/search. Go to record. Click pencil icon <img width=30 src=img/edit.png>to edit  </li>
	
</ul>


';
//////////////user code ends////////////////
tail();

//echo '<pre>';print_r($_POST);print_r($_FILES);echo '</pre>';

//////////////Functions///////////////////////

?>
