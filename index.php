<?php
	require_once("admin/inc/config.php");
	include_once("./cryptofunction.php");

	$fetchingElections = mysqli_query($conn,"SELECT * FROM elections") OR die(mysqli_error($conn));
	while($data = mysqli_fetch_assoc($fetchingElections))
	{
		$stating_date = $data['starting_date'];
		$ending_date = $data['ending_date'];
		$curr_date = date("Y-m-d");
		$election_id = $data['id'];
		$status = $data['status'];

		// Active = Expire = Ending Date
		// InActive = Active = Starting Date

		if($status == "Active")
		{
			$date1=date_create($curr_date);
        	$date2=date_create($ending_date);
        	$diff= date_diff($date1,$date2);

        	if((int)$diff->format("%R%a") < 0)
        	{
				mysqli_query($conn, "UPDATE elections SET status = 'Expired' WHERE id= '". $election_id ."' ") OR 
				die(mysqli_error($conn));
        	}
		}else if($status == "InActive")
		{
			$date1=date_create($curr_date);
        	$date2=date_create($stating_date);
        	$diff= date_diff($date1,$date2);

        	if((int)$diff->format("%R%a") <= 0)
        	{
				mysqli_query($conn, "UPDATE elections SET status = 'Active' WHERE id= '". $election_id ."' ") OR 
				die(mysqli_error($conn));
        	}
		}
	}
?>
<!DOCTYPE html>
<html>   
<head>
	<title>Voting System</title>
	<link rel="stylesheet" href="./assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="./assets/css/login.css">
</head>
<body>
	<div class="container h-100">
		<div class="d-flex justify-content-center h-100">
			<div class="user_card">
				<div class="d-flex justify-content-center">
					<div class="brand_logo_container">
						<img src="./assets/Images/FairVote_logo_2022.svg.png" class="brand_logo" alt="Logo">
					</div>
				</div>
				<?php
					if(isset($_GET['sign-up']))  
					{
						?>
							<div class="d-flex justify-content-center form_container">
								<form method="post">
									<div class="input-group mb-3">
										<div class="input-group-append">
											<span class="input-group-text"><i class="fas fa-user"></i></span>
										</div>
										<input type="text" name="su_username" class="form-control input_user" placeholder="username" required />
									</div>
									<div class="input-group mb-2">
										<div class="input-group-append">
											<span class="input-group-text"><i class="fas fa-key"></i></span>
										</div>
										<input type="text" name="su_contact_number" class="form-control input_pass"  placeholder="contact #". required />
									</div>
									<div class="input-group mb-2">
										<div class="input-group-append">
											<span class="input-group-text"><i class="fas fa-key"></i></span>
										</div>
										<input type="password" name="su_password" class="form-control input_pass"  placeholder="password". required />
									</div>
									<div class="input-group mb-2">
										<div class="input-group-append">
											<span class="input-group-text"><i class="fas fa-key"></i></span>
										</div>
										<input type="password" name="su_retype_password" class="form-control input_pass"  placeholder="retype password". required />
									</div>
										<div class="d-flex justify-content-center mt-3 login_container">
								<button type="submit" name="sign_up_btn" class="btn login_btn">Sign up</button>
							</div>
								</form>
							</div>
					
							<div class="mt-4">
								<div class="d-flex justify-content-center links">
									Already have an account! <a href="index.php" class="ml-2">Sign in</a>
								</div>
							</div>
						<?php

					}else {
				?>

								<div class="d-flex justify-content-center form_container">
								<form method="post">
									<div class="input-group mb-3">
										<div class="input-group-append">
											<span class="input-group-text"><i class="fas fa-user"></i></span>
										</div>
										<input type="text" name="contact_no" class="form-control input_user" placeholder="Contact_no" required />
									</div>
									<div class="input-group mb-2">
										<div class="input-group-append">
											<span class="input-group-text"><i class="fas fa-key"></i></span>
										</div>
										<input type="password" name="password" class="form-control input_pass" value="" placeholder="password" required />
									</div>
									<div class="form-group">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="customControlInline">
											<label class="custom-control-label" for="customControlInline">Remember me</label>
										</div>
									</div>
										<div class="d-flex justify-content-center mt-3 login_container">
								<button type="submit" name="loginbtn" class="btn login_btn">Login</button>
							</div>
								</form>
							</div>
					
							<div class="mt-4">
								<div class="d-flex justify-content-center links">
									Don't have an account? <a href="?sign-up=1" class="ml-2">Sign Up</a>
								</div>
								<div class="d-flex justify-content-center links">
									<a href="#">Forgot your password?</a>
								</div>
							</div>
				<?php
					}
				?>
			<?php
				if(isset($_GET['registered']))
				{
			?>
				<span class="bg-white text-success text-center my-3">Your account has been created successfully!</span>
				<?php
				}else if(isset($_GET['invalid'])){
				?>
					<span class="bg-white text-danger text-center my-3">Password mismatched,please later again!</span>
				<?php
				}else if(isset($_GET['not_registered'])){
				?>
					<span class="bg-white text-warning text-center my-3">you are not registered!</span>
				<?php
				}else if(isset($_GET['invalid_access'])){
				?>
					<span class="bg-white text-danger text-center my-3">invalid contact_no or password!</span>
				<?php
				}
			?>
			</div>
		</div>
	</div>
	<script src="./assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>

<?php
	include_once("./cryptofunction.php");
	include_once("./admin/inc/config.php");

	if(isset($_POST['sign_up_btn']))
	{
		$su_username=mysqli_real_escape_string($conn, $_POST['su_username']);
		$su_contact_number=mysqli_real_escape_string($conn, $_POST['su_contact_number']);
		$su_password=mysqli_real_escape_string($conn,$_POST['su_password']);
		$su_password=encryptthis($su_password, $key);
		$su_retype_password=mysqli_real_escape_string($conn,$_POST['su_retype_password']);
		// $su_retype_password=encryptthis($su_retype_password, $key);
		$user_role = "Voter";
    
        mysqli_query($conn, "INSERT INTO users(username ,contact_no ,password, user_role )VALUES
		('". $su_username ."','". $su_contact_number ."','". $su_password ."', '". $user_role ."')") or 
		die(mysqli_error($conn));

		// 	?>
		// 		<script>location.assign("index.php?sign-up=1&registered=1");</script>
		// 	<?php
		// }else{
		// 	?>
		// 		<script>location.assign("index.php?sign-up=1&invalid=1");</script>
		// 	<?php
		// }
	}
	else if(isset($_POST['loginbtn']))
	{

		$contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
		$password = mysqli_real_escape_string($conn,$_POST['password']);

		$fetchingData = mysqli_query($conn, "SELECT * FROM users WHERE contact_no ='". $contact_no ."'")
		 or die(mysqli_error($conn));
		if(mysqli_num_rows($fetchingData) > 0)
		{
			$data = mysqli_fetch_assoc($fetchingData);
			if($contact_no == $data['contact_no'])
			// $password == $data['password']
			{ 
				session_start();
				$_SESSION['user_role'] = $data['user_role'];
				$_SESSION['username'] = $data['username'];
				$_SESSION['user_id'] = $data['id'];

				if($data['user_role'] == "Admin")
				{
					$_SESSION['key'] ="AdminKey";
			?>
				<script>location.assign("admin/index.php?homepage=1");</script>
			<?php
				}else{
					$_SESSION['key'] ="VotersKey";
			?>
				<script>location.assign("voters/index.php?homepage=1");</script>
			<?php
				}
			}else{
				?>
				<script>location.assign("index.php?invalid_access=1");</script>
			<?php
			}
		}else{
			?>
				<script>location.assign("index.php?sign-up=1&not_registered=1");</script>
			<?php
			}

		}
	
?>