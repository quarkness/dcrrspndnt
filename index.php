<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>de correspondent, de artikelen</title>
		<link rel="stylesheet" href="./style.css" />
		<link rel="alternate" type="application/rss+xml" title="Artikelen van De Correspondent - crrspndnt" href="./rss.php">
	</head>
	<body>


<?php
require_once('settings.local.php');
include('db.php');
$count_res = mysql_query('select count(*) as amount from artikelen');
$count_arr = mysql_fetch_array($count_res);
$tot_row = $count_arr['amount'];
$start = 0;
if(isset($_GET['page']))
{
	$page = (int)$_GET['page'];
	$start = ($page - 1) * ITEMS_PER_PAGE;
	if($start < 0) $start = 0;
}
$i = 0;
$res = mysql_query('select * from artikelen order by created_at desc limit '.$start.','.ITEMS_PER_PAGE);
?>
		<h1>Artikelen van <a href="http://decorrespondent.nl/">de Correspondent</a> gevonden op Twitter</h1>
<?php include ('menu.php'); ?>
		<table>
			<tr>
				<th>Opgedoken</th><th>Titel / Artikel</th><th>Auteur</th><th>Sectie</th>
			</tr>
<?php
while($row = mysql_fetch_array($res) )
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],26);
	$description = isset($og['description']) ? $og['description'] : 'Een mysterieus artikel';
	$auth_res = mysql_query('select * from meta where meta.waarde = "'.$og['article:author'].'"');
	$author = mysql_fetch_array($auth_res);
	$section_res = mysql_query('select * from meta where meta.waarde = "'.$og['article:section'].'"');
	$section = mysql_fetch_array($section_res);
	?>

	<tr <?php if($i % 2 == 1) echo 'class="odd"'?>>
		<td><?php echo substr($row['created_at'],8,2); echo '-'.substr($row['created_at'],5,2).' '.substr($row['created_at'],11,5) ?></td>
		<td><strong><a href="<?php echo $row['share_url'];?>" title="<?php echo $description ?>"><?php echo $titel ;?></a></strong></td>
		<td><a href="./meta_art.php?id=<?php echo $author['ID'];?>" title="alle artikelen van deze auteur"><?php echo $author['waarde'];?></a></td>
		<td><a href="./meta_art.php?id=<?php echo $section['ID'];?>" title="alle artikelen in deze sectie"><?php echo $section['waarde'];?></a></td>
	</tr>
	<?php
	$i++;
}
?>
</table>
<ul id="pager">
	<?php
	// how many pages?
	$pages = ceil($tot_row / ITEMS_PER_PAGE);
	$i = 0;
	while ($i < $pages)
	{
		$page = $i + 1;
		echo '<li><a href="./?page='.$page.'">'.$page.'</a></li>';
		$i++;
	}
	?>
</ul>
<?php include('footer.php') ?>
</body>
<?php @include('ga.inc.php') ?>

</html>

<?php
/*
alle artikelen per auteur:
select *, count(meta_artikel.id) from meta join meta_artikel on meta_artikel.meta_id = meta.ID where type = 'article:author' group by meta.ID
en per sectie:
select *, count(meta_artikel.id) from meta join meta_artikel on meta_artikel.meta_id = meta.ID where type = 'article:section' group by meta.ID
*/