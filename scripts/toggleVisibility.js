function toggleVisibility(id) {
		var element = document.getElementById(id);
		var img = document.getElementById('toggle' + id);
		if (element.style.display != 'none' ) {
				element.style.display = 'none';
				img.src='images/maximize.gif'
		} else {
				element.style.display = '';
				img.src='images/minimize.gif'
		}
		
}
