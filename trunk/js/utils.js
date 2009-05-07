/*
    Copyright (C) 2006-2009 Regis PHILIPPE

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/* Set or access to the "hidden" property of a given element ID */
function hide(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		thisElement.style.display = 'none';
	}
}
function hideChildren(id, start) {
	thisElement = document.getElementById(id);
	for (var i=start; i<thisElement.childNodes.length; i++) {
		if (thisElement.childNodes[i].nodeType == 1) {
			thisElement.childNodes[i].style.display = 'none';
		}
	}
}
function isHidden(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		if ((thisElement.style.display) && (thisElement.style.display == 'none')) {
			return true;
		}
		else {
			return false;
		}
	}
}

/* Set or access to the "visible" property of a given element ID */
function show(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		thisElement.style.display = '';
	}
}
function showChildren(id, start) {
	thisElement = document.getElementById(id);
	for (var i=start; i<thisElement.childNodes.length; i++) {
		if (thisElement.childNodes[i].nodeType == 1) {
			thisElement.childNodes[i].style.display = '';
		}
	}
}
function isVisible(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		if ((!thisElement.style.display) || (thisElement.style.display != 'none')) {
			return true;
		}
		else {
			return false;
		}
	}
}

/* Display or hide a given element ID */
function changeDisplay(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		if (thisElement.style.display == 'none') {
			thisElement.style.display = '';
		}
		else {
			thisElement.style.display = 'none';
		}
	}
}

/* Access or modify value of a given element ID */
function getValue(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		return thisElement.value;
	}
	else {
		return false;
	}
}
function setValue(id, value) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		thisElement.value = value;
	}
}
function cleanValue(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		thisElement.value = '';
	}
}
function emptyValue(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		if (thisElement.value == '') {
			return true;
		}
		else {
			return false;
		}
	}
}

/* Access or modify inner HTML content of a given ID element */
function getContent(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		return thisElement.innerHTML;
	}
	else {
		return false;
	}
}
function setContent(id, content) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		thisElement.innerHTML = content;
	}
}
function cleanContent(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		thisElement.innerHTML = '';
	}
}
function emptyContent(id) {
	thisElement = document.getElementById(id);
	if (thisElement) {
		if (thisElement.innerHTML == '') {
			return true;
		}
		else {
			return false;
		}
	}
}

/* Check or uncheck all checkboxes of a given form ID */
function checkAll(id) {
	var thisForm = document.getElementById(id + 'form');
	for (var i=0; i<thisForm.elements.length; i++) {
		if(thisForm.elements[i].type == 'checkbox') {
			thisForm.elements[i].checked = document.getElementById(id + 'checkall').checked;
		}
	}
}

/* Display or hide a content box with the appropriate "more" or "less" hyperlink */
function moreInfo() {
	show('more_data');
	hide('more_link');
	show('less_link');
}
function lessInfo() {
	hide('more_data');
	hide('less_link');
	show('more_link');
}
