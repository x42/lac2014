function externalLinks() {
  if (!document.getElementsByTagName) return;
  var anchors = document.getElementsByTagName("a");
  for (var i=0; i<anchors.length; i++) {
    var anchor = anchors[i];
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "_blank")
      anchor.target = "_blank";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "parent")
      anchor.target = "_parent";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "external")
      anchor.target = "_blank";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "wiki")
      anchor.target = "lac-wiki";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "supporter")
      anchor.target = "lac-sponsor";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "registration")
      anchor.target = "lac-registration";
  }
}
window.onload = externalLinks;

function inlineInfoBox(id){
  document.getElementById('infobox').style.display = "inline";
  document.getElementById('dimmer').style.display = "inline";
  document.getElementById('infobox').scrollTop=0; /* FIXME: scrollTop is not compatible */
}

function showInfoBox(id){
  if (document.getElementById('ieframe')) {
    document.getElementById('ieframe').src  = 'raw.php?pdb_filterid='+id;
  } else {
    document.getElementById('infoframe').data = 'raw.php?pdb_filterid='+id;
  }
  inlineInfoBox();
}

function showInfoPix(id){
  if (document.getElementById('ieframe')) {
    document.getElementById('ieframe').src  = 'pix.php?id='+id;
  } else {
    document.getElementById('infoframe').data = 'pix.php?id='+id;
  }
  inlineInfoBox();
}
function hideInfoBox() {
  if (document.getElementById('ieframe')) {
    document.getElementById('ieframe').src  = 'raw.php';
  } else {
    document.getElementById('infoframe').data = 'raw.php';
  }
  document.getElementById('infobox').style.display = "none";
  document.getElementById('dimmer').style.display = "none";
}

function formsubmit(id) {
  if (document.getElementById(id)) {
    document.getElementById(id).submit();
	}
}

function adminjump(hashtag) {
  if (document.getElementById(hashtag)) {
		window.location.hash=hashtag;
	}
}

function admingo(page,mode,param) {
	document.getElementById('page').value=page;
	document.getElementById('mode').value=mode;
	document.getElementById('param').value=param;
	formsubmit('myform');
}

function onCloseWarn(e) {
	e = e || window.event;
	if (e) {
		e.returnValue = 'You should navigate away using the SAVE or CANCEL button to release the edit-lock.\n\nData you have entered may not be saved.';
	}
	return 'You should navigate away using SAVE or CANCEL button to release the edit-lock.\n\nData you have entered may not be saved.';
}

function setonclosewarn() {
  window.onbeforeunload = onCloseWarn;
}

function noonclosewarn() {
  window.onbeforeunload = null;
}
