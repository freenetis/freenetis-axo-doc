<!DOCTYPE html>
<?php
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	
	if (!file_exists("js/translate.$lang.js"))
	{
		$lang = 'en';
	}
?>
<html>
	<head>
		<title>FreenetIS AXO documentation</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css" media="screen">
		<link rel='stylesheet' type='text/css' href='skin/ui.dynatree.css'>
		<script src='js/jquery.min.js' type="text/javascript"></script>
		<script src='js/jquery-ui.custom.min.js' type="text/javascript"></script>
		<script src='js/jquery.dynatree.min.js' type="text/javascript"></script>
		<script src='js/translate.<?php echo $lang; ?>.js' type="text/javascript"></script>
		<script src='js/script.js' type="text/javascript"></script>
	</head>
	<body>
		<div id="loading"></div>
		<div class="hide" id="header">
			<h1>Free<span>net</span>IS</h1>
			<div class="t">AXO documentation</div>
			<ul class="tab">
				<li class="tab" id="sources_tab"><span class="t">Pages</span></li>
				<li class="tab tab_link" id="axo_sections_tab"><span class="t">Access rights</span></li>
				<li id="tab_help"></li>
			</ul>
			<div class="clear"></div>
		</div>
		
		<div class="hide" id="content-padd">
			<div id="tree">
			<div id="sources_tree"></div>
			<div id="sections_tree" class="hide"></div>
			</div>
			<div id="details">
				<div id='content'>
					<div id="breadcrumbs">&nbsp;</div>
					<h2></h2>
					<div id="subtitle"></div>
					<br/>
					<div id="show_detail" class="hide"></div>
					<div id="node_detail" class="hide"></div>
					<div id="method_detail" class="hide">
						<div><img src="js/skin/access.png"><span class="t">access rights for access to page</span></div>
						<table>
							<tbody id="access_table">
								<tr>
									<th>AXO value</th>
									<th>AXO</th>
									<th>ACO</th>
									<th class="t">Actions</th>
								</tr>
							</tbody>
						</table>
						<div><img src="js/skin/access-partial.png"><span class="t">access rights for access to part of page</span></div>
						<table>
							<tbody id="access-partial_table">
								<tr>
									<th>AXO value</th>
									<th>AXO</th>
									<th>ACO</th>
									<th class="t">Actions</th>
								</tr>
							</tbody>
						</table>
						<div><img src="js/skin/links.png"><span class="t">Access rights for links to other pages</span></div>
						<table>
							<tbody id="links_table">
								<tr>
									<th>AXO value</th>
									<th>AXO</th>
									<th>ACO</th>
									<th class="t">Actions</th>
								</tr>
							</tbody>
						</table>
						<div><img src="js/skin/breadcrumbs.png"><span class="t">Access rights for breadcrumbs navigation</span></div>
						<table>
							<tbody id="breadcrumbs_table">
								<tr>
									<th>AXO value</th>
									<th>AXO</th>
									<th>ACO</th>
									<th class="t">Actions</th>
								</tr>
							</tbody>
						</table>
						<div><img src="js/skin/grid-action.png"><span class="t">Access rights for grid actions</span></div>
						<table>
							<tbody id="grid-action_table">
								<tr>
									<th>AXO value</th>
									<th>AXO</th>
									<th>ACO</th>
									<th class="t">Actions</th>
								</tr>
							</tbody>
						</table>
					</div>
					<table id="axo_detail" class="hide">
						<tbody>
							<tr>
								<th>AXO:</th>
								<td id="axo"></td>
							</tr>
							<tr>
								<th>ACO:</th>
								<td id="aco"></td>
							</tr>
							<tr>
								<th class="t">Usage:</th>
								<td id="usage"></td>
							</tr>
						</tbody>
					</table>
					<div id="comments" class="hide">
						<h3 class="t">Comment</h3>
						<div id="comment"></div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
