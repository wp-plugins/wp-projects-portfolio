function showpasscode(div_id, hlink) {
  jQuery("html, body").animate({ scrollTop: '850px'}, 800);
  var hclass = jQuery(hlink).attr("class");
	if(hclass == 'hide-button') {
		jQuery('#'+div_id).fadeIn('slow');
		jQuery(hlink).attr("class","disp-button");
	}
	else {
		jQuery('#'+div_id).fadeOut('slow');
		jQuery(hlink).attr("class","hide-button");
	}
}
function submit_crdata(pcode,pid)
{

jQuery("#pc-label").html('<span class="perror-msg">Please wait......</span>');
jQuery.ajax({type: 'POST',url: crAjax.ajaxurl,data: {action: 'crpcode_nonce',wpppcode:pcode,wpppid:pid},
success:function(response, textStatus, XMLHttpRequest){
if(textStatus == 'success') {
	if(response == 'spcode') {
	jQuery("#crresp").html("<div align='center' class='success-spc'>Passcode Verified!</div>");
	jQuery('#rlickbutton').hide();
	jQuery('#crFormbox').hide();
	jQuery('#crboxcontainer').fadeIn('slow');
	} else if(response == 'fpcode') {
	jQuery("#pc-label").html("<span class='perror-msg'>Invalid Passcode! Please try again.</span>");
	} else {
	jQuery("#pc-label").html("<span class='perror-msg'>Please enter your passcode</span>");
	}
}

}, 
error: function(MLHttpRequest, textStatus, errorThrown){
alert(errorThrown);
}
});
}
function submit_recommendation(crcontent,pid)
{
jQuery("#recom-label").html('<span class="perror-msg">Please be wait......</span>');

jQuery.ajax({type: 'POST',url: crAjax.ajaxurl,data: {action: 'crcontent_nonce',crboxcontent:crcontent,crpid:pid},
success:function(response, textStatus, XMLHttpRequest){
if(textStatus == 'success') {
	if(response == 'scrcont') {
	jQuery("#recom-label").hide();
	jQuery("#crboxcontainer").fadeOut();
	jQuery("#crresp").html("<div align='center' class='success-crc'>Successfully Added! Thank you.</div>");
	} else if(response == 'fcrcont') {
	jQuery("#recom-label").html("<span class='perror-msg'>Error! Please try again.</span>");
	} else {
	jQuery("#recom-label").html("<span class='perror-msg'>Please fill the recommendation field</span>");
	}
}

}, 
error: function(MLHttpRequest, textStatus, errorThrown){
alert(errorThrown);
}
});
}

function clientrec_disp_status(pid,dstatus)
{
jQuery.ajax({type: 'POST',url: crAjax.ajaxurl,data: {action: 'crsts_nonce',crpid:pid,crsts:dstatus},
success:function(response, textStatus, XMLHttpRequest){
}, 
error: function(MLHttpRequest, textStatus, errorThrown){
alert(errorThrown);
}
});
}