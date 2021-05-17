<?php
$con = mysqli_connect('localhost','root','','youtube');
$parent = 0;
$error = 'not found';
?>
<p id="tree"></p>
<?php
if (isset($_GET['name']) || isset($_GET['type'])) {
	$arr = [];
	if(isset($_GET['type']))
	{
		
		$res = mysqli_query($con,"select * from cate where type = '".$_GET['type']."' && parent_id = 0");
		if (mysqli_num_rows($res) > 0) {
			echo '<script>document.getElementById("tree").innerHTML = "'.$_GET['type'].'/"</script>';
			# code...
		}
		else
		{
			$url = "Location: dtree.php?error="."type ".$_GET['type']."  ".$error;
			header($url);
			//echo "<script>alert('Not Found');</script>";
		}
		$parent = 0;
	}
	if (isset($_GET['name'])) {
		$res1 = mysqli_query($con,"select * from cate where name = '".$_GET['name']."'");
		$row1 = mysqli_fetch_assoc($res1);
		if (mysqli_num_rows($res1) > 0) {
			$res = mysqli_query($con,"select * from cate where parent_id='".$row1['id']."'");
			# code...
		}
		else
		{
			$res = null;
			//header("Location: dtree.php");
		}
		//print_r(mysqli_num_rows($res));
		if ($res != null) {
			if (mysqli_num_rows($res) == 0) {
				echo "<ol><li>".$row1['name'];
			}	
		}
		if ($res != null) {
			$parent = $row1['id'];
			$index = $row1['parent_id'];
			$subtree = "";
			if ($index == 0) {
				$subtree = "<a href='dtree.php?name=".$row1['name']."'>".$row1['name']."</a>";
				$tree = $row1['type'].'/';
				//$pret = "<a href='dtree.php?name=".$_GET['name']."' >".$_GET['name']."</a>";
				echo '<script>document.getElementById("tree").innerHTML = "'.$tree.$subtree.'/"</script>';
			}
			else
			{
				while ($index > 0) {
					$result = mysqli_query($con,"select * from cate where id = ".$index);
					$row2 = mysqli_fetch_assoc($result);
					//print_r($row2);
					$subtree = "<a href='dtree.php?name=".$row2['name']."'>".$row2['name']."</a>/".$subtree;
					$index = $row2["parent_id"];
					//print_r($index);
				}
				$tree = $row1['type'].'/';
				$pret = "<a href='dtree.php?name=".$_GET['name']."' >".$_GET['name']."</a>";
				echo '<script>document.getElementById("tree").innerHTML = "'.$tree.$subtree.$pret.'/"</script>';
			}
			# code...
		}
		else
		{
			header("Location: dtree.php");
		}
		
	}
	if (isset($_GET['name']) && isset($_GET['type'])) {
		$query = "select * from cate where type = '".$_GET['type']."' && name = '".$_GET['name']."'";
		$res1 = mysqli_query($con,$query);
		$row1 = mysqli_fetch_assoc($res1);
		if (mysqli_num_rows($res1) > 0) {
			$res = mysqli_query($con,"select * from cate where parent_id = '".$row1['id']."'");
			# code...
		}
		else
		{
			$res = null;
		}
		if ($res != 0) {
			$parent = $row1['id'];
			$index = $row1['parent_id'];
			//print_r($row1['parent_id']);
			$subtree = "";
			if ($index == 0) {
				$subtree = "<a href='dtree.php?name=".$row1['name']."'>".$row1['name']."</a>";
				$tree = $row1['type'].'/';
				echo '<script>document.getElementById("tree").innerHTML = "'.$tree.$subtree.'/"</script>';
			}
			else
			{
				while ($index > 0) {
					$result = mysqli_query($con,"select * from cate where id = ".$index);
					$row2 = mysqli_fetch_assoc($result);
					$subtree = "<a href='dtree.php?name=".$row2['name']."'>".$row2['name']."</a>/".$subtree;
					$index = $row2["parent_id"];
				}
				$tree = $row1['type'].'/';
				$pret = "<a href='dtree.php?name=".$_GET['name']."' >".$_GET['name']."</a>";
				//print_r($pret);
				echo '<script>document.getElementById("tree").innerHTML = "'.$tree.$subtree.$pret.'/"</script>';
			}
			# code...
		}
		else
		{
			header("Location: dtree.php");
		}
		
	}

if ($res != null) {
	while ($row = mysqli_fetch_assoc($res)) {
		$arr[$row['id']]['name'] = $row['name'];
		$arr[$row['id']]['parent_id'] = $row['parent_id'];
		$arr[$row['id']]['type'] = $row['type'];
	}
	# code...
}

buildTreeView($con,$arr,$parent);
}
else
{
	?>
	<form method="GET" action="dtree.php" id='form1'>
		<input type="text" name="name">
	</form>
	<button form="form1"> search</button>
	<br>
	<?php

	echo "<a href='dtree.php?type=goods'>Goods</a><br>";
	echo "<a href='dtree.php?type=service'>Service</a>";
}



function buildTreeView($con,$arr,$parent,$level=0,$prelevel=-1)
{
	foreach ($arr as $id => $data) {
		if ($parent == $data['parent_id']) {
			if ($level > $prelevel) {
				echo "<ol>";
			}
			if ($level == $prelevel) {
				echo "</li>";
			}
			$res3 = mysqli_query($con,"select count(parent_id) as c from cate where parent_id='".$id."'");
			$row2 = mysqli_fetch_assoc($res3);
			if ($row2['c'] == 0) {
				echo "<li>".$data['name'];
			}
			else
			{
				echo "<li><a href='dtree.php?name=".$data['name']."&type=".$data['type']."'>".$data['name']."</a></li>";
			}
			if ($level > $prelevel) {
				$prelevel = $level;
			}
			//$level++;
			//buildTreeView($arr,$id,$level,$prelevel);
			//$level--;
		}
	}
}
mysqli_close($con);
?>
<br>
<br>
<br>
<a href="dtree.php">home</a>

<?php
	if (isset($_GET['error'])) {
		echo "<h1>".$_GET['error']."</h1>";
		# code...
	}
?>


