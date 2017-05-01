$(document).ready(function() {
    $('.submitButton').click(function(e) {
      e.preventDefault();
      $form=$(this).parents('form');
      $form.find('.has-error').removeClass('has-error');
      $form.find('.messages').html('');
      validateForm($form);
    });
});


function validateForm($form, $function){
  $.blockUI({
    message: 'processing...',
    overlayCSS: { backgroundColor: '#00f' }
  });
	$.ajax({
		url:$form.attr('action'),
		method:"POST",
		dataType:'JSON',
  	async: false,
		data:$form.serializeArray(),
		success:function(data){
      if(data.status) {
        if(data.url) window.location.replace(data.url);
        else $.unblockUI();
      }
		},
		error:function(data){
      console.log(data.responseJSON);
      var errorMessage = '';
      $.each(data.responseJSON, function(x,y) {
        $form.find('#' + x).addClass('has-error');
        errorMessage += '<li>' + y + '</li>'
        console.log(y);
      })
      $form.find('.messages').html(errorMessage);
      // unblock when remote call returns
      $.unblockUI();
		},
    complete: function() {
        // unblock when remote call returns
        $.unblockUI();
    }
	});
}
