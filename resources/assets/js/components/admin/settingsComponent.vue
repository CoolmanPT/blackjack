<template>
    <div class="container">
    	<div class="row">
			<div class="col-sm-12">
				<h1 class="text-left mt-2">
					Administrator Settings
				</h1>
				<hr>
			</div>
		</div>
    	<div class="row">
    		<div class="col-sm-12">
    			<ul class="nav nav-tabs nav-fill mt-5" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Change Platform Email
 
 
</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Change Administrator Email</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Change Administrator Password
    </a>
  </li>
</ul>
<div class="tab-content border border-top-0 pt-3" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
  	<platform-email-settings-component></platform-email-settings-component>
  </div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
  	<admin-change-email-component></admin-change-email-component>
  </div>
  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
  	<admin-change-password-component></admin-change-password-component>
  </div>
</div>
    		</div>
    	</div>
    	
    </div>
</template>