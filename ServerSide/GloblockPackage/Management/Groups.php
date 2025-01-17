<!DOCTYPE HTML>
<!--
	Alex Quigley
	x10205691
	Globlock | 2 Factor Document Access Control, Repository and File Tokenization
	4th year Project for BSHCE4 Networking and Mobile, with the National College of Ireland
-->
<html>
	<head>
		<title>Globlock - Groups</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic,700" rel="stylesheet" />
		<script src="js/jquery.min.js"></script>
		<script src="js/config.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/view.js"></script>
		
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
		</noscript>
		<!--[if lte IE 9]><link rel="stylesheet" href="css/style-ie9.css" /><![endif]-->
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
	</head>
	<body>

		<!-- Header -->

			<div id="header-wrapper">
				<header class="container" id="site-header">
					<div class="row">
						<div class="12u">
							<div id="logo">
								<h1>Globlock</h1>
							</div>
							<nav id="nav">
								<ul>
									<li><a href="index.html">Homepage</a></li>
									<li><a href="HowTo.html">How To</a></li>
									<li><a href="Globes.php">Globes</a></li>
									<li><a href="Documents.php">Documents</a></li>
									<li class="current_page_item"><a href="Groups.php">Groups</a></li>
									<li><a href="Users.php">Users</a></li>
									<li><a href="Repository.html">Repository</a></li>
								</ul>
							</nav>
						</div>
					</div>
				</header>
			</div>

		<!-- Main -->

			<div id="main-wrapper" class="subpage">
				<div class="container">
					<div class="row">
						<div class="6u skel-cell-important">
					
							<!-- Content -->
							<article class="first">
							
								<div class="form_description">
									<h3>Group List</h3>
								</div>	
										
								<table id="hor-minimalist-a">
									<tr>
										<th>Group name</th>
										<th>Description</th>
										<th>Date Added</th>
									</tr>
										<?php
											include '../package/management/e_grouptable.php';
										?>
								</table>
							
							</article>		

						</div>
						<div class="6u">
						
							<section>
							
								<div class="form_description">
									<h3>New Group</h3>
								</div>	
								
								<p>Enter a name and description for your new Group.</p>
						
								<form id="form_754783" class="appnitro"  method="post" action="../package/management/e_newgroup.php">
									<ul >
										<li id="li_1" >
											<label class="description" for="element_1">Group Name </label>
											<div>
												<input id="element_1" name="groupname" class="element text large" type="text" maxlength="255" value=""/> 
											</div>
											<p class="guidelines" id="guide_1"><small>Choose a name that best summarizes the Group and/or it's members. Such as ''Design Team', 'Development', 'Finance - Ireland', 'Contractors' etc...</small></p> 
										</li>		
										
										<li id="li_2" >
											<label class="description" for="element_2">Group Description </label>
											<div>
												<textarea id="element_2" name="groupdesc" class="element textarea medium"></textarea> 
											</div>
											<p class="guidelines" id="guide_2"><small>Provide a brief description of the Group, it's users or it's function. It may also be wise to add any descriptive comments that such as the groups goals, projects or type of documents they will likely work on.</small></p> 
										</li>
										
										<li class="buttons">
											<input type="hidden" name="form_id" value="754783" />
											<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
										</li>
									</ul>
								</form>	
							
							</section>							

							<!-- Sidebar -->
							
							<section>
								<div class="form_description">
									<h3>Ipsum Dolor</h3>
								</div>	
							
								<ul class="link-list">
									<li><a href="#">Sed dolore viverra</a></li>
									<li><a href="#">Ligula non varius</a></li>
									<li><a href="#">Nec sociis natoque</a></li>
									<li><a href="#">Penatibus et magnis</a></li>
									<li><a href="#">Dis parturient montes</a></li>
									<li><a href="#">Nascetur ridiculus</a></li>
								</ul>
							</section>

							<section class="last">
								<div class="form_description">
									<h3>Magna Phasellus</h3>
								</div>	
								<p>
									Vehicula fermentum ligula at pretium. Suspendisse semper iaculis eros, eu aliquam 
									iaculis. Phasellus ultrices diam sit amet orci lacinia sed consequat. 							
								</p>
								<ul class="link-list">
									<li><a href="#">Sed dolore viverra</a></li>
									<li><a href="#">Ligula non varius</a></li>
									<li><a href="#">Dis parturient montes</a></li>
									<li><a href="#">Nascetur ridiculus</a></li>
								</ul>
							</section>
						
						</div>
				</div>
			</div>

		<!-- Footer -->

			<div id="footer-wrapper">
				<footer class="container" id="site-footer">
					<div class="row">
						<div class="3u">
							<section class="first">
								<h2>Ipsum et phasellus</h2>
								<ul class="link-list">
									<li><a href="#">Mattis et quis rutrum sed accumsan</a>
									<li><a href="#">Suspendisse amet varius nibh</a>
									<li><a href="#">Suspenddapibus amet mattis quis</a>
									<li><a href="#">Rutrum accumsan eu varius</a>
									<li><a href="#">Nibh lorem sed dolore et ipsum.</a>
								</ul>
							</section>
						</div>
						<div class="3u">
							<section>
								<h2>Lorem mattis dolor</h2>
								<ul class="link-list">
									<li><a href="#">Duis neque nisi dapibus sed</a>
									<li><a href="#">Suspenddapibus amet mattis quis</a>
									<li><a href="#">Rutrum accumsan eu varius</a>
									<li><a href="#">Nibh lorem sed dolore et ipsum.</a>
									<li><a href="#">Mattis et quis rutrum sed accumsan</a>
								</ul>
							</section>
						</div>
						<div class="3u">
							<section>
								<h2>Mattis quis tempus</h2>
								<ul class="link-list">
									<li><a href="#">Suspendisse amet varius nibh</a>
									<li><a href="#">Suspenddapibus amet mattis quis</a>
									<li><a href="#">Rutrum accumsan eu varius</a>
									<li><a href="#">Nibh lorem sed dolore et ipsum.</a>
									<li><a href="#">Duis neque nisi dapibus sed</a>
								</ul>
							</section>
						</div>
						<div class="3u">
							<section class="last">
								<h2>Odio et phasellus</h2>
								<ul class="link-list">
									<li><a href="#">Rutrum accumsan eu varius</a>
									<li><a href="#">Nibh lorem sed dolore et ipsum.</a>
									<li><a href="#">Duis neque nisi dapibus sed</a>
									<li><a href="#">Mattis et quis rutrum sed accumsan</a>
									<li><a href="#">Suspendisse amet varius nibh</a>
								</ul>
							</section>
						</div>
					</div>
					<div class="row">
						<div class="12u">
							<div class="divider"></div>
						</div>
					</div>
					<div class="row">
						<div class="12u">
							<div id="copyright">
								&copy; Untitled. All rights reserved. | Design: <a href="http://html5up.net">HTML5 UP</a> | Images: <a href="http://fotogrph.com">fotogrph</a>
							</div>
						</div>
					</div>
				</footer>
			</div>

	</body>
</html>