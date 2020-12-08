<?php require APPROOT . '/views/inc/header.php'; ?>
<!-- Add that Canvas JS -->
	<a href="<?php echo URLROOT; ?>/posts" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
	
		<h2>Add Post</h2>
		<p>Create a post with this form</p>		
		<div class="container-fluid">
			<div class="row">
				<!-- Main -->
				<div class="col-lg-8">
					<div class="card card-body bg-light mt-5">
					<!-- Canvas And Stickers -->
						<div class="row">
							<div class="col">
								<!-- Overlay canvas -->
								<img id="uploadimg" style="display:none"/>
								<video id="video" class="img-fluid" autoplay></video>

								<div class="edit" id="editEnable" style="display:none">
									<div class="row">
										<div style="position: relative;">
											<canvas id="canvas" width="500" height="500" class="img-fluid" style="border: 1px solid gray"></canvas>
											<canvas id="canvasstickers" ondrop="drop(event)" ondragover="allowDrop(event)" width="500" height="500" class="img-fluid" style="position: absolute; left: 0; top: 0; z-index: 1;"></canvas>
										</div>
										<div class="stickers" style="width:100%;background-color:black;overflow-y:scroll;height: 10vh;">
											<?php
												foreach ($data['files'] as $key=>$file)
												{
													echo '<img id="img'. $key .'"  draggable="true" ondragstart="drag(event)" src='. $file .' style="height: 10vh;">';
												}
											?>
										</div>
									</div>
									<div class="row">
										<div class="col">
											<button class="btn btn-success" onclick="retake()">Retake</buttone>
										</div>
										<div class="col">
											<button class="btn btn-success" onclick="clearStickers()">Clear Stickers</button>
										</div>
									</div>
								</div>	
							</div>

							
						</div>
						<!-- Upload / Snap -->
						<div class="row" id="snapControll">
								<div class="col">
									<button id="snap" class="btn btn-success">Snap Photo</button>
								</div>
								<div class="col">						
										<form action="" method="post" enctype="multipart/form-data">
											<label class="custom-file" for="customInput">
												<input type="file" accept="image/*" onchange="loadFile(event)" class="custom-file-input">
												<span class="custom-file-control form-control-file"></span>
											</label>
										</form> 
								</div>

						</div>		

						<div class="row">
							<!-- Actual Form -->
							<div class="col">
								<form action="<?php echo URLROOT; ?>/posts/add" method="post" id="PostForm" onsubmit="appendSubmit()">
									<div class="form-group">
										<label for="title">Title: <sup>*</sup></label>
										<input type="text" name="title" class="form-control form-control-lg <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['title']; ?>">
										<span class="invalid-feedback"><?php echo $data['title_err']; ?></span>
									</div>
									<div class="form-group">
										<label for="body">Body: <sup>*</sup></label>
										<textarea name="body" class="form-control form-control-lg <?php echo (!empty($data['body_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['body']; ?></textarea>
										<span class="invalid-feedback"><?php echo $data['body_err']; ?></span>
									</div>
									<input type="submit" id="PostBtn" class="btn btn-success" value="Submit" disabled>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- SideNav -->
				<div class="col-lg-2">
					<div class="card card-body bg-light mt-5">
					</div>
				</div>
			</div>
		</div>
	

<!-- FOOTER -->
<script src="<?php echo URLROOT; ?>/js/cam.js"></script>
<?php require APPROOT . '/views/inc/footer.php'; ?>