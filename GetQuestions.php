<?php

require_once("DbConfig.php");

$db = new DbConfig();
$query= $db->db_connect();
$q = $query->prepare("select * from question q order by rand()");
$q->execute();
$rows = $q->fetchAll(PDO::FETCH_ASSOC);
echo "<select placeholder='Question' class='form-control' name='question'>";
echo "<option disabled>Questions</option>";                               
                                
foreach ($rows as $row) 
{
	 echo '<option value="'.$row["name"].'">'.$row["name"]."</option>";
}

echo '</select>';

$query = $db->db_disconnect();

?>