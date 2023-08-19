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
echo strftime("%Y-%m-%d %h:%m:%s");

echo '<form method=post>';
echo 'Set Date:<input name=date type=date>';
echo '<input type=submit name=action value="set date">';
echo '<input type=hidden name=session_name  value="'.$_POST['session_name'].'">';
echo '</form>';

if(isset($_POST['date']))
{
        $show_date=$_POST['date'];      
}
else
{
        $show_date=date("Y-m-d");
}


if(isset($_POST['tname']))
{
	if($_POST['tname']=='outword')
	{
		$extra=array
		(
			['type'=>'print_lable','label'=>'','target'=>' target=_blank ','action'=>'action=print_lable.php']
		);
	}
	else
	{
		$extra=array();
	}
}
else
{
	$extra=array();
}

echo '<div class="two_column_one_by_two">';
	echo '<div>';
		list_available_tables($link);
	echo '</div><div>';
		manage_stf($link,$_POST,$show_crud='yes',$extra,$order_by=' order by  id desc ');
		echo '<div id=element_for_child>Child Data</div>';
	echo '</div>';
echo '</div>';

        $sql='
(       
select 
        i.id,i.date,f.food_item,i.gm,
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


                                        
)
        union
(
select 
        "GFR","required","For","'."Filteration".'",
        "mL/min",
        round(sum((f.protein*i.gm)/f.weight)*0.5723 ,1) as  Protein,
        round(sum((f.potassium*i.gm)/f.weight*0.004456) ,1) as Potassium,
        round(sum((f.phosphorus*i.gm)/f.weight)*0.01736 ,1) as  Phosphorus,
        round(sum((f.calcium*i.gm)/f.weight)*0.00772 ,1) as  Calcium,
        round(sum((f.sodium*i.gm)/f.weight)*0.000216 ,2) as  Sodium,
        "",
        "",
        "",
        ""
        from myfood f, intake i
                        where 
                                        date=\''.$show_date.'\'
                                        and
                                        f.id=i.food_id



) 
                                       ';
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
if($_POST['action']=='make_myfood')
{
$make_myfood_sql='
insert into myfood 
(id,food_item,weight,calories,calcium,phosphorus,potassium,sodium,protein,fat,carbohydrate,fiber,recorded_by,recording_time)
(select 
NULL, r.name,  sum(r.gm),
sum(m.calories*(r.gm/m.weight)),sum(m.calcium*(r.gm/m.weight)),sum(m.phosphorus*(r.gm/m.weight)),
sum(m.potassium*(r.gm/m.weight)),sum(m.sodium*(r.gm/m.weight))	,sum(m.protein*(r.gm/m.weight))	,sum(m.fat*(r.gm/m.weight))	,
sum(m.carbohydrate*(r.gm/m.weight))	,sum(m.fiber*(r.gm/m.weight)),null,null
from myfood m, recipe r 
where recipe_id=\''.$_POST['recipe_id'].'\' and r.food_id=m.id)
';
echo $make_myfood_sql;

run_query($link,$GLOBALS['database'],$make_myfood_sql);

}
//////////////user code ends////////////////
tail();

//echo '<pre>';print_r($_POST);print_r($_FILES);echo '</pre>';
//////////////Functions///////////////////////

?>
<form method=post>
<h5>enter recipe id to convert it into myfood</h5>
Recipe ID:<input type=number name=recipe_id>
<input type=submit name=action value=make_myfood>
<input type=hidden name=session_name  value='<?php echo $_POST["session_name"]; ?>'>
</form>

    <table border=1><colgroup><col width="99"/><col width="99"/><col width="99"/><col width="99"/><col width="99"/><col width="99"/><col width="99"/></colgroup><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>Protein</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>Nitogen</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>Urea</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>Serum</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>6.5</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>1</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>2.14285714285714</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>0.4</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>mg/ml</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>18.5</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>2.84615384615385</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>6.0989010989011</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>15247.2527472527</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>10.58836996337</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml/min</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>GFR required</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>Potassium</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>mg</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>mmol</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>serum</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>39</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>1</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>4</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>mmol</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>1000</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>727</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>18.6410256410256</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>18.6410256410256</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>4660.25641025641</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>3.23628917378917</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml/min</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>Phosphorus</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>mg</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>P</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>0.04</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>mg/ml</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>1</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>310</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>310</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>7750</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>5.38194444444444</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml/min</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>Calcium</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>mg</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>P</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>0.09</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>mg/ml</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>1</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>464</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>5155.55555555556</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>3.58024691358025</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml/min</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>Sodium</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"><p>mg</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>mmol</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>serum</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>23</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>1</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>140</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>mmol</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>1000</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>74</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>3.21739130434783</p></td><td style="text-align:right; width:2.258cm; " class="Default"><p>3.21739130434783</p></td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>22.9813664596273</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml</p></td></tr><tr class="ro1"><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:left;width:2.258cm; " class="Default"> </td><td style="text-align:right; width:2.258cm; " class="Default"><p>0.01595928226363</p></td><td style="text-align:left;width:2.258cm; " class="Default"><p>ml/min</p></td></tr></table>



<script>
function run_ajax(str,rid)
{
	//create object
	xhttp = new XMLHttpRequest();
	
	//4=request finished and response is ready
	//200=OK
	//when readyState status is changed, this function is called
	//responceText is HTML returned by the called-script
	//it is best to put text into an element
	xhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		//document.getElementById(rid).innerHTML = document.getElementById(rid).innerHTML+this.responseText;
		document.getElementById(rid).innerHTML = this.responseText;
	  }
	};
	//Setting FORM data
	xhttp.open("POST", "display_child.php", true);
	
	//Something required ad header
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
	// Submitting FORM
	xhttp.send(str);
	
	//used to debug script
	//alert("Used to check if script reach here");
}

/*
Array
(
    [action] => child
    [session_name] => sn_1368472133
    [fname] => resident_id
    [fvalue] => 4
    [tname] => attendance
)
*/

function make_post_string(fname,fvalue,tname,session_name)
{
	//k=encodeURIComponent(t.id);					//to encode almost everything
	//v=encodeURIComponent(t.value);					//to encode almost everything
	post='fname='+fname+'&fvalue='+fvalue+'&tname='+tname+'&session_name='+session_name;
	return post;							
}

function do_work(fname,fvalue,tname,session_name)
{
	//alert("doing work");
	str=make_post_string(fname,fvalue,tname,session_name);
	//alert(post);
	run_ajax(str,'element_for_child');
}

</script>
