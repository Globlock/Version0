<!DOCTYPE HTML>
<!--
	Alex Quigley
	x10205691
	Globlock | 2 Factor Document Access Control, Repository and File Tokenization
	4th year Project for BSHCE4 Networking and Mobile, with the National College of Ireland
-->
<html>
	<head>
		<title>Globlock - Globes</title>
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
									<li class="current_page_item"><a href="Globes.php">Globes</a></li>
									<li><a href="Documents.php">Documents</a></li>
									<li><a href="Groups.php">Groups</a></li>
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
									<h3>Globe List</h3>
								</div>	
										
									<table id="hor-minimalist-a">
										<tr>
											<th>Globe name</th>
											<th>Description</th>
											<th>Date Added</th>
										</tr>
										<?php
											include '../package/management/e_globetable.php';
										?>
									</table>
								
							</article>		

						</div>
						
						<div class="6u">
						
							<section>
							
								<form id="form_754783" class="appnitro"  method="post" action="../package/management/e_newglobe.php">
								
									<div class="form_description">
										<h3>New Globe Object</h3>
									</div>						
									
									<p>Enter a name and description for your new Globe Project.</p>
									
									<ul>
										<li id="li_1" >
											<label class="description" for="element_1">Globe name</label>
											<div>
												<input id="element_1" name="globename" class="element text large" type="text" maxlength="255" value=""/> 
											</div>
												<p class="guidelines" id="guide_1"><small>Choose a name that best summarizes the Globes and/or it's contents.</small></p> 
										</li>		
										
										<li id="li_2" >
											<label class="description" for="element_2">Globe Description </label>
											<div>
												<textarea id="element_2" name="globedesc" class="element textarea medium"></textarea> 
											</div>
											<p class="guidelines" id="guide_2"><small>Provide a brief description of the Globe, it's contents or it's function. It may also be wise to add any descriptive comments that you may wish all users to view before opening the Globe, such as the documents' functions or projects aim or goals.</small></p> 
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