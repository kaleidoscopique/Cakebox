/*
 * Create the XMLHTTPREQUEST OBJECT
 */
function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
}

/*
 * A filter for file on index.php (all/seen/notseen/videos)
 */
function filesfilter(oSelect) {
	var value = oSelect.options[oSelect.selectedIndex].value;
	var xhr   = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
		{
			document.getElementById("local").innerHTML = xhr.responseText;
			//document.getElementById("loader").style.display = "none";
		} else if (xhr.readyState < 4)
		{
			//document.getElementById("loader").style.display = "inline";
		}
	};
	if(value.search("-edit") != -1) xhr.open("GET", "xhr_request.php?get_list&filter="+value+"&editmode", true);
	else xhr.open("GET", "xhr_request.php?get_list&filter="+value, true);
	xhr.send(null);
}

/*
 * Mark a file as seen on watch.php
 */
function markfile(filename)
{
	var xhr   = getXMLHttpRequest();
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
		{
			var divSelect = document.getElementById("popcorn");
			divSelect .innerHTML = "All right, <span class=\"unmark\">you have marked this video</span> as \"seen\" !";
			//document.getElementById("loader").style.display = "none";
		} else if (xhr.readyState < 4)
		{
			//document.getElementById("loader").style.display = "inline";
		}
	};
	
	xhr.open("GET", "xhr_request.php?mark_file&file_name="+filename, true);
	xhr.send(null);
}

/*
 * Unmark a file as "seen" when user click "cancel" on watch.php
 */
function unmarkfile(filename)
{
	var xhr   = getXMLHttpRequest();
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
		{
			var divSelect = document.getElementById("popcorn");
			divSelect .innerHTML = "Ok, <span class=\"mark\">you have unmarked this video</span>.";
			//document.getElementById("loader").style.display = "none";
		} else if (xhr.readyState < 4)
		{
			//document.getElementById("loader").style.display = "inline";
		}
	};
	
	xhr.open("GET", "xhr_request.php?unmark_file&file_name="+filename, true);
	xhr.send(null);
}

/*
 * Delete a file when user click "delete"
 */
function unlink(oSelect)
{
	var xhr   = getXMLHttpRequest();
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
		{
			var divSelect = document.getElementById("div-"+oSelect.id);
			divSelect .innerHTML = "";
			document.getElementById("div-"+oSelect.id).style.display = "none";
			//document.getElementById("loader").style.display = "none";
		} else if (xhr.readyState < 4)
		{
			//document.getElementById("loader").style.display = "inline";
		}
	};
	
	xhr.open("GET", "xhr_request.php?drop_file&file_name="+oSelect.id, true);
	xhr.send(null);
}

/**
 * Check all the files of a dir in editmode when the user check a dir
 */
function CheckLikes(dir)
{
		if(dir.checked == false) var state = false;
		else var state = true;
		boxes = document.editform.Files.length;
		for (i = 0; i < boxes; i++)
		{
			if (document.editform.Files[i].value.search(dir.value) != -1)
			{
				document.editform.Files[i].checked = state;
			}
		}
}

function showhidedir(elem)
{
	etat=document.getElementById(elem).style.display;
	if(etat=="none"){
	document.getElementById(elem).style.display="block";
	}
	else{
	document.getElementById(elem).style.display="none";
	}
}