<!DOCTYPE HTML>
<!--
	Alex Quigley
	x10205691
	Globlock | 2 Factor Document Access Control, Repository and File Tokenization
	4th year Project for BSHCE4 Networking and Mobile, with the National College of Ireland
-->
<html>
	<head>
		<title>Globlock - Users</title>
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
									<li><a href="Groups.php">Groups</a></li>
									<li class="current_page_item"><a href="Users.php">Users</a></li>
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
									<h3>User List</h3>
								</div>	
										
								<table id="hor-minimalist-a">
									<tr>
										<th>Username</th>
										<th>Last Name</th>
										<th>Email</th>
										<th>Group</th>
										<th>SuperUser</th>									
									</tr>
										<?php
											include '../package/management/e_usertable.php';
										?>
								</table>
							
							</article>		

						</div>
						<div class="6u">
						
							<section>
							
								<div class="form_description">
									<h3>New User</h3>
								</div>	
								<p>Enter the required details for your new Globlock User.</p>
						
								<form id="form_754783" class="appnitro"  method="post" action="../package/management/e_newuser.php">
									
									<ul >
										<li id="li_2" >
											<label class="description" for="element_2">Name </label>
											<span>
												<input id="element_2_1" name= "first_name" class="element text" maxlength="255" size="14" value=""/>
												<label>First</label>
											</span>
											<span>
												<input id="element_2_2" name= "last_name" class="element text" maxlength="255" size="14" value=""/>
												<label>Last</label>
											</span> 
											<p class="guidelines" id="guide_4"><small>User name First and Last.</small></p> 
										</li>		
										
										<li id="li_3" >
											<label class="description" for="element_3">Email </label>
											<div>
												<input id="element_3" name="email" class="element text medium" type="text" maxlength="255" value=""/> 
											</div>
											<p class="guidelines" id="guide_4"><small>User email address.</small></p> 
										</li>		
										
										<li id="li_7" >
											<label class="description" for="element_7">Username </label>
											<div>
												<input id="element_7" name="username" class="element text medium" type="text" maxlength="255" value=""/> 
											</div>
											<p class="guidelines" id="guide_4"><small>Username for system login.</small></p>
										</li>		
										
										<li id="li_4" >
											<label class="description" for="element_4">Temporary Password </label>
											<div>
												<input id="element_4" name="password" class="element text medium" type="text" maxlength="255" value=""/> 
											</div>
											<p class="guidelines" id="guide_4"><small>User will be prompted to change at first login.</small></p> 
										</li>		
										
										<li id="li_1" >
											<label class="description" for="element_1">Department / Cost Code </label>
											<div>
												<input id="element_1" name="dept_code" class="element text medium" type="text" maxlength="255" value=""/> 
											</div>
											<!--<p class="guidelines" id="guide_1"><small>The department / cost code field is optional, but may be useful to populate for reporting purposes at a later stage.</small></p> -->
											<p class="guidelines" id="guide_1"><small>The department / cost code field .</small></p> 
										</li>		
										
										<li id="li_6" >
											<label class="description" for="element_6">Primary Group </label>
											<div>
												<select class="element select medium" id="element_6" name="group"> 
													<option value="0" selected="selected">Undefined</option>
													<?php
														include '../package/management/e_groupids.php';
													?>
												</select>
											</div>
											<p class="guidelines" id="guide_6"><small>A primary group is not required but may make it easier if defined at the start.</small></p> 
										</li>		
										
										<li id="li_5" >
											<label class="description" for="element_5">User Type </label>
											<div>
												<select class="element select medium" id="element_5" name="user_type"> 
													<option value="0" selected="selected">Regular System User</option>
													<option value="1" >Super User (Admin)</option>
												</select>
											</div>
											<p class="guidelines" id="guide_5"><small>Please ensure you have chosen the correct Option as superusers have the ability to modify all aspects of the Globlock system.</small></p> 
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
							
							<section class="last">							
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