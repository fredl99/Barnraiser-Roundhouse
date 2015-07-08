
//puts browser window at top (out of frames) - stops bug with registering from inside hotmail frame.
if (self != top){
   if (document.images) top.location.replace(document.location.href);
   else top.location.href = document.location.href;
}


// used to create a layer as a tooltip
function showTooltip (id, tooltip) {
	var curleft = curtop = 0;

	var tooltip = document.getElementById(tooltip);

	tooltip.style.top = id.offsetTop+20+"px";
	tooltip.style.left = id.offsetLeft+"px";

	tooltip.style.display = 'block';
}

function hideTooltip (id, tooltip) {
	var tooltip = document.getElementById(tooltip);

	tooltip.style.display = 'none';
}

function objShowHide(id) {
	
	if (document.getElementById) {
		if (document.getElementById(id).style.display == 'block') {
			document.getElementById(id).style.display = 'none';
		}
		else {
			document.getElementById(id).style.display = 'block';
		}
	}
	else {
		if (document.layers) {
			if (document.id.visibility == 'block') {
				document.id.visibility = 'none';
			}
			else {
				document.id.visibility = 'block';
			}
		}
		else { // IE 4
			if (document.all.id.style.display == 'block') {
				document.all.id.style.display = 'none';
			}
			else {
				document.all.id.style.display = 'block';
			}
		}
	}
}


function get(id) {
	return document.getElementById(id);
}
			
function ajax() {
	var xmlHttp;
	try {
		xmlHttp=new XMLHttpRequest(); 
	}
	catch (e) {
		try	{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
				return false;
			}
		}
	}
	return xmlHttp;
}


