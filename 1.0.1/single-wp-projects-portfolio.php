<?php get_header(); ?>

 <!-- main container-->
            <div class="main-container">
			  <h1><strong>PROJECT DETAILS</strong></h1>
			    <div class="project-detail">
<?php
	
	global $post;
	
	if ( have_posts() ) : while ( have_posts() ) : the_post();

		$description = '';
			$description = get_the_content();
			$description = apply_filters('the_content', $description);
		$type = get_post_meta(get_the_ID(), "_wpprojects_portfolio_type", true);
		$portfolio_type = get_term_by( 'slug', $type, 'portfolio_type' );
		if (isset($portfolio_type->name)) {
			$type = $portfolio_type->name;
		} else {
			$type = "";
		}
		$datecreate =  get_post_meta(get_the_ID(), "_createdate", true);
		$client = get_post_meta(get_the_ID(), "_clientname", true);
		$technical_details = get_post_meta(get_the_ID(), "_technical_details", true);
		$siteurl = get_post_meta(get_the_ID(), "_siteurl", true);
		$project_imgurl = get_post_meta(get_the_ID(), "_imageurl", true);
		$project_problem = get_post_meta(get_the_ID(), "_projectproblem", true);
		$project_solution = get_post_meta(get_the_ID(), "_projectsolution", true);
?>

<div class="detail-lft">
		
		<?php							
		if ( (!empty($project_imgurl)) ) {
		$original_image = str_replace( '-235x150.jpg' ,'.jpg', $project_imgurl);
	
		$imageurl = '<a href="'.$original_image.'" target="_blank"><img src="'.$original_image.'" border="0" alt="" width="414" height="420"></a>';
		} else {
		
		$imageurl = '<img src="' .plugins_url( 'images/no-pimage.gif' , __FILE__ ). '" width="200" height="150"/> ';
		}
		?>
		<?php echo $imageurl; ?>
		
		<?php if ( !empty($siteurl)) { ?>
		<a href="<?php echo $siteurl; ?>" target="_blank"><?php echo $siteurl; ?></a>
		<?php } ?>
                    
</div>

 <div class="detail-rgt">
                        <h2><?php echo the_title_attribute('echo=0'); ?></h2>
						<?php if ( !empty($siteurl)) { ?>
						<div class="detail-submit">
						<a href="<?php echo $siteurl; ?>" target="_blank">Click here to see the site</a>
						<div class="clear"></div>
						</div>
						<?php } ?>
						
					<?php
					if ( (!empty($description)) ) {
					
					$description_value = $description;
					
					} else {
					
					$description_value = '......';
					
					}
					?>


                        <h3>Requirement</h3>
                       <?php echo $description_value; ?>
                        
                    </div>
                   <div class="clear"></div>
				   


  <div class="project-prb">
  <?php if ( !empty($project_problem) || !empty($project_solution) ) : ?>
                       <h3>Problem & Solutions</h3>
	<?php endif; ?>
                       <div class="prob">
                           <ul>
                              
							   <?php if ( !empty($project_problem)) { ?>
							    <li>
                                   <div class="prob-left">
                                       <img src="<?=plugins_url( 'images/prob.jpg' , __FILE__ );?>" width="207" height="26" alt="">
                                   </div>
                                   <div class="prob-right">
                                      <p class="tm-alright">
									<?php echo $project_problem; ?>
									   </p>
                                   </div>
                                   <div class="clear"></div>
								   
								    </li>
									
								   <?php } ?>
								   
                              
							   	<?php if ( !empty($project_solution)) { ?>

                               <li>
                                   <div class="prob-left">
                                       <img src="<?=plugins_url( 'images/solution.jpg' , __FILE__ );?>" width="207" height="26" alt="">
                                   </div>
                                   <div class="solution-right">
									<p class="tm-alright">
									
									<?php echo nl2br($project_solution); ?>
									
									
									</p>
									</div>
								</li>
								<?php } ?>
		
	

		<?php 
$dbcr_status = get_post_meta($post->ID , "_wpp_crstatus", true); 
$cl_photo = get_post_meta($post->ID , "_clientphoto", true); 
$dbclient_rec = get_post_meta($post->ID , "_clientrecommendation", true); 
if($dbclient_rec):
if($dbcr_status == 'on'):

?>

  <!---clent Testimonal -->
<li>
<div class="client-testimonal">
	 <div class="prob-left">
     <img src="<?=plugins_url( 'images/client-testimonial.jpg' , __FILE__ );?>" width="207" height="26" alt="">
      </div><br>
	<div class="client-container">
		<div class="client-img">
			<img src="<?php  if(!empty($cl_photo)): echo $cl_photo; else: echo plugins_url( 'images/ct-avatar.png' , __FILE__ ); endif;?>" width="124" height="123" alt="">
		</div>
		<div class="client-cont">
			<p class="rec-alright">
				<?php echo $dbclient_rec;?>   
			
			 <span>-- <?php if ( !empty($client)) { echo $client; } ?> <br>&nbsp;&nbsp;<?php if ( !empty($technical_details)) { echo "  ".$technical_details; } ?></span>
			 </p>
		</div>
		<div class="clear"></div>
	</div>
	
</li>
 <!---end of clienttestimonal -->
														

<?php 
endif;
else: 
$dbcr_pcode = get_post_meta($post->ID , "_wpp_passcode", true);
if(!empty($dbcr_pcode)) {
?>

<!---testimonal -->
<div class="testimonal">
   <a href="#clt-testimonial" id="rlickbutton" onclick="showpasscode('crFormbox', this)" class="hide-button">Submit your Testimonial</a>
  
</div>
<div class="clear"></div>
<!---end testimonal -->


<?php } 
endif; ?>

<li>
	<!---passcode -->
<div id="crFormbox" class="passcode">
<form id="crForm" method="post" name="crForm">
<div align="center" class="pc-label" id="pc-label"></div>
<div id="crForm-inner" class="dp_contact_name-passcode">
<?php $cr_nonce = wp_create_nonce("crvalidate_nonce"); ?>
<strong>&nbsp;Your Passcode:</strong> <input id="wppassc" name="wppassc" class="input" value= "" type="text" size="15"/>
<input name="wpppid" type="hidden" value="<?php echo $post->ID;?>" />
<input id="crnonce" type="hidden" value="<?php echo $cr_nonce; ?>"/>
<input name="action" type="hidden" value="crpcode_nonce" />&nbsp;
<input name="action" type="hidden" value="crpcode_nonce" />&nbsp;</div>
<div class="passcode-readmore">
<a href="#clt-testimonial" class="crForm-inner-img" align="right" onClick="submit_crdata(document.crForm.wppassc.value,document.crForm.wpppid.value);">Verify</a>
</div>
 </div>
		<div class="clear"></div>
	</div>
	<!---end passcode -->
	
</form>
</div><div id="crresp" align="center"></div>
<div id="crboxcontainer"><div align="center" class="recom-label" id="recom-label">Please enter your recommendation here</div>
<form name="crecommend" id="crecommend" action="" method="post">
<?php $crbox_nonce = wp_create_nonce("crboxcont_nonce"); ?>
<input id="crboxnonce" name="crboxnonce" type="hidden" value="<?php echo $crbox_nonce; ?>"/>
<input name="crpid" type="hidden" value="<?php echo $post->ID;?>" />
<input name="action" type="hidden" value="crcontent_nonce" />&nbsp;
<textarea id="crboxcont" name="crboxcont" rows="5" cols="25" class="crbox" style="height:120px; width:380px;"></textarea><br>
<div align="center">
<input type="button" name="csubmit" id="csubmit" value="Submit" class="crboxsubmit" onClick="submit_recommendation(crboxcont.value,crpid.value);">
</div>
</form>
</li>
</ul>
</div>

</div>

</div>

				
		
<?php	endwhile; endif;?>
			<div style="clear:both;"></div> </div>
			 
        <!-- end of main container-->

<?php get_footer(); ?>