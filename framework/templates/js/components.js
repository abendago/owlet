$(document).ready(function(){
	
	// dynamically add hint value into text fields
	$("input[hint]").each(function()
	{
		objCycled= $(this);
		objCycled.val(objCycled.attr("hint"));
	});

	$("input[hint]").focus(function()
	{
		objFocused = $(this);
		if (objFocused.val()=="")
		{
			objFocused.val(objFocused.attr("hint"));
		}
	});

	// credit: http://stackoverflow.com/questions/10550717/regex-for-url-validation-using-jquery 10/29/2013
	function fnCheckUrl(url){
		return url.match(/(http|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/);
	}

	var fnSubmitURL = function (){

		var strURL = $("#strURL").val();

		
		if ($(this).hasClass("btnShorten"))
		{
			var strAPIURL = "http://short.abendago.com/api/encode/json";
		} else {
			var strAPIURL = "http://short.abendago.com/api/decode/json";
		}

		$("#responseElement").removeClass().html("<img src='/templates/images/ajaxloader.gif' class='loader'/>").show();

		// local error checking first, no point in posting if there is an error already
		// use a try catch so we can easily add other checks in the future
		try
		{
			if (!fnCheckUrl(strURL))
			{
				throw "Invalid URL";
			}

			$.ajax(
			{
				url: strAPIURL+"?callback=?",
				data: {"strProvidedURL": strURL},
				dataType: 'json',
				success: function(objJSON) {
					$("#responseElement h2").html(objJSON.strMessage);
					if (objJSON.nStatus==1)
					{
						$("#responseElement").hide().addClass("success").html("<h2>"+objJSON.strMessage+"</h2><h3>"+objJSON.strResultURL+"</h3>").fadeIn();
					} else {
						$("#responseElement").hide().addClass("error").html("<h2>"+objJSON.strMessage+"</h2>").fadeIn();
					}
				},
				timeout: 7000, // incase the server takes too long to respond we dont want the user just waiting
				error: function(jqXHR, status, errorThrown){   
					 $("#responseElement").addClass("error").html("<h2>Looks Like Our Friends Failed To Respond...</h2><p>This could happen if the server is unavailable or busy. It could also be that your Internet connection was a bit too slow. Try again?"); 
				} 
			});
		}
		catch (error)
		{
			$("#responseElement").addClass("error").html("<h2>Something Didn't Work Out</h2><p>You may have an error in your URL so make sure you included an http:// or https:// at the front!</p>");
		}
	}

	$(".btnShorten, .btnDecode").click(fnSubmitURL);

});