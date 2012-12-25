//Author: Gowtham
//08:11:00 12/24/2011
var core = {
    user: null,
    authenticated: false,
    userPic: new Image(),
    statusField: null,
    init: null,
    transit: null,
    body: null,
    header: null,
    userInfo: {
	name: this.user,
	pic: this.userPic,
	positions: {
	    INDIVIDUAL: {
		label: 'INDIVIDUAL',
		func: '',
		aL: '',
		oid: '',
		id: ''
	    }
	},
	role: 'INDIVIDUAL'
    },
    onSignOutScripts: {},
    htmlEncode: function(s) {
	var el = document.createElement("div");
	el.innerText = el.textContent = s;
	s = el.innerHTML;
	return s;
    },
    htmlDecode: function(s) {
	var el = document.createElement("div");
	el.innerHTML = s;
	s = el.textContent;
	return s;
    },
    panelize: function() {
	if (this.panel && !this.panel.children[this.body.id]) {
	    if (!this.panel.parentElement) {
		this.body.parent.appendChild(this.panel);
	    }
	    this.panel.appendChild(this.body.parentElement.removeChild(this.body));
	    this.panel.style.display = null;
	    this.body.classList.add('panelBody');
	    this.parentElement.style.display = 'none';
	    this.panel.reAlign();
	} else {
	    this.panel = new core.msgPanel(this.body.name, this.body.parentElement, this, this.body, null);
	    this.panel.minimizeBtn = this.parentElement.children['hide'].cloneNode(true);
	    this.parentElement.style.display = 'none';
	    this.panel.dePanelizeBtn = this.cloneNode(true);
	    this.panel.dePanelizeBtn.body = this.body;
	    this.panel.dePanelizeBtn.panel = this.panel;
	    this.panel.dePanelizeBtn.style.position = 'absolute';
	    this.panel.dePanelizeBtn.style.top = '4px';
	    this.panel.dePanelizeBtn.style.right = '14px';
	    this.panel.appendChild(this.panel.dePanelizeBtn);
	    this.panel.minimizeBtn.style.position = 'absolute';
	    this.panel.minimizeBtn.style.top = '4px';
	    this.panel.minimizeBtn.style.right = '26px';
	    this.panel.dePanelizeBtn.onclick = core.dePanelize;
	    if (this.panel.offsetWidth > innerWidth * 0.9)
		this.panel.style.width = innerWidth * 0.9 + "px";
	    if (this.panel.offsetHeight > innerHeight * 0.9)
		this.panel.style.height = innerHeight * 0.9 + "px";
	    this.panel.reAlign();
	}
	//return false;
    },
    dePanelize: function() {
	this.body.parent.appendChild(this.panel.removeChild(this.body));
	this.body.mrc.style.display = null;
	this.body.style.width = null;
	this.body.style.height = null;
	this.body.classList.remove('panelBody');
	this.panel.style.display = 'none';
	//return false;
    },
    assistant: function() {
	this.__proto__ = new Image();
	this.src = '/images/arrow.png';
	this.style.display = 'none';
	this.style.position = 'absolute';
	core.body.appendChild(this.__proto__);
	this.__proto__.setPosition = function(elm) {
	    this.style.left = (elm.offsetLeft + elm.offsetWidth) + 'px';
	    this.style.top = elm.offsetTop + 'px';
	    this.style.display = null;
	    this.timeOut = window.setTimeout(function() {
		assistant.style.display = 'none'
	    }, 3000);
	}
	return false;
    },
    expressText: function(text) {
	return 'e: ' + text;
    },
    signIn: function(username, password) {
	core.statusField.innerHTML = 'Signing In...'
	var content = "username=" + username + "&password=" + core.MD5(password);
	var feed = new Object();
	var postExpedition = function(feed) {
	    if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'failure') {
		core.statusField.innerHTML = "Incorrect username or password.~:|~";
	    } else if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'success') {
		core.authenticated = true;
		core.user = username;
		window.sessionId = feed.responseXML.getElementsByTagName('sessionid')[0].textContent;
		document.getElementById('password').value = "";
		var positions = feed.responseXML.getElementsByTagName('position');
		for (var i = 0; i < positions.length; i++) {
		    var property = 'o' + positions[i].getElementsByTagName('oid')[0].textContent;
		    core.userInfo.positions[property] = {};
		    core.userInfo.positions[property].label = positions[i].getElementsByTagName('label')[0].textContent;
		    core.userInfo.positions[property].func = positions[i].getElementsByTagName('func')[0].textContent;
		    core.userInfo.positions[property].aL = positions[i].getElementsByTagName('al')[0].textContent;
		    core.userInfo.positions[property].oid = positions[i].getElementsByTagName('oid')[0].textContent;
		    core.userInfo.positions[property].id = positions[i].getElementsByTagName('id')[0].textContent;
		}
		try {
		    core.userPic.src = "userFiles/" + core.user + "/" + feed.responseXML.getElementsByTagName('userPic')[0].firstChild.nodeValue;
		} catch (e) {
		    core.userPic.src = ''
		}
		;
		core.transit();
		core.statusField.innerHTML = "SignIn success.~:)~";
	    }
	    else {
		core.statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].textContent;
	    }
	}
	feed.ferry = new core.shuttle('lib/signIn.php', content, postExpedition, feed);
    },
    signOut: function() {
	core.statusField.innerHTML = 'Signing out...'
	var feed = new Object();
	feed.postExpedition = function(feed) {
	    if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'failure') {
		core.statusField.innerHTML = 'Sign out failed ~:|~ retry or contact <a>administrator</a>';
	    } else if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'signedOut') {
		delete window.sessionId;
		core.clientSignOut();
		return true;
	    } else {
		core.statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].textContent;
	    }
	    return false;
	}
	feed.content = {};
	feed.ferry = new core.shuttle('lib/signOut.php', feed.content, feed.postExpedition, feed);
	return false;
    },
    clientSignOut: function() {
	core.authenticated = false;
	core.user = "guest";
	core.userInfo.positions = {
	    INDIVIDUAL: {
		label: 'INDIVIDUAL',
		func: '',
		aL: '',
		oid: '',
		id: ''
	    }
	};
	for (var i in core.onSignOutScripts) {
	    core.onSignOutScripts[i]();
	}
	core.transit();
	core.statusField.innerHTML = "Signed out..";
	return true;
    },
    genForm: function(formMod, ctrller) {
	statusField.innerHTML = "generating " + formMod + "...";
	var page = document.getElementById(formMod + 'Page');
	if (page == null || page.children.length == 0 || page.offsetHeight != 0) {
	    if (page == null) {
		page = document.createElement('div');
		page.id = formMod + 'Page';
		page.classList.add('userSpecific');
		core.body.appendChild(page);
	    }
	    core.loadAsGadget2("lib/" + formMod + ".php", null, page);
	} else {
	    if (core.formatPage(formMod, ctrller)) {
		core.statusField.innerHTML = "Here is ur " + formMod + " ~:)~";
	    }
	}
	return false;
    },
    afterDocGenScript: function(formMod, ctrller) {
	switch (formMod) {
	    case('registerationForm'):
		core.dateFieldFill();
		core.formatPage(formMod, ctrller);
		break;
	    case('editProfileForm'):
		ctrller.disabled = false;
		core.dateFieldFill();
		editProfileForm = document.getElementById('editProfileForm').children;
		var userPicArea = editProfileForm['userPicBox'].children[0].children[0];
		if (!core.userPic.exists) {
		    document.getElementById('uploadBtnTxt').innerHTML = 'Add a pic';
		} else {
		    if (core.userPic.parentElement)
			core.userPic.parentElement.removeChild(core.userPic);
		    userPicArea.appendChild(core.userPic);
		}
		core.formatPage(formMod);
		break;

	    case('userProfile'):
		ctrller.disabled = false;
		document.getElementById('userPicArea').appendChild(core.userPic);
		core.formatPage('userProfile');
		break;
	}
    },
    dateFieldFill: function(dateDiv) {
	var yrArray = dateGen.year();
	var yrElm = dateDiv.children['year'];
	for (var i = yrArray.length - 1; i >= 0; i--) {
	    yrElm.options[yrElm.options.length] = new Option(yrArray[i], yrArray[i], false, false);
	}
	var mnthArray = dateGen.month();
	var mnthElm = dateDiv.children['month'];
	for (var i = 0; i < mnthArray.length; i++) {
	    mnthElm.options[i + 1] = new Option(mnthArray[i].monthName, mnthArray[i].monthNum, false, false);
	}
    },
    customUploadFunction: function(uploadType) {
	if (uploadType == "ProfPic") {
	    var userEditPic = editProfileForm['userPicBox'].children[0].children[0].children['userEditPic'];
	    userEditPic.src = core.userPic.src;
	    userEditPic.style.display = null;
	    if (!core.userPic.exists)
		document.getElementById('uploadBtnTxt').innerHTML = 'Change Pic';
	}
    },
    hideAllChildElements: function(htmlObjId) {
	var elm = document.getElementById(htmlObjId);
	for (var i = 0; i < elm.children.length; i++) {
	    elm.children[i].style.display = 'none';
	}
    },
    setDay: function(dateDiv) {
	var year = dateDiv.children['year'].value;
	var month = dateDiv.children['month'].value;
	var dayElm = dateDiv.children['day'];
	dayElm.innerHTML = "";
	dayElm.options[0] = new Option("Day:", 0, true, false);
	if (year != 0 && month != 0) {
	    var dayArray = dateGen.day(year, month);
	    for (var i = 0; i < dayArray.length; i++) {
		dayElm.options[i + 1] = new Option(dayArray[i], dayArray[i], false, false);
	    }
	}
    },
    alignDoc: function() {
	core.mbody.style.height = (innerHeight - 16) + 'px';
	core.mbody.style.width = (innerWidth - 16) + 'px';
	core.header.style.width = core.mbody.style.width;
	if (window.search)
	    search.reAlign();
    },
    formatPage: function(view, ctrller) {
	view = view.toString()
	switch (view) {
	    case ("home"):
		if (core.authenticated) {
		    document.getElementById('signInTool').style.display = 'none';
		    document.getElementById('register_Btn').style.display = 'none';
		    document.getElementById('register_Btn').disabled = true;
		    document.getElementById('user').innerHTML = core.user;
		    document.getElementById('signInTool').children['username'].value = core.user;
		    document.getElementById('welcomeUser').style.display = null;
		    document.getElementById('userProfileBtn').style.display = null;
		    document.getElementById('userProfileBtn').disabled = false;
		    document.getElementById('signOutTool').style.display = null;
		    document.getElementById('roleTool').style.display = null;
		    document.getElementById('searchMouth').focus();
		} else {
		    document.getElementById('signInTool').style.display = null;
		    document.getElementById('register_Btn').style.display = null;
		    document.getElementById('register_Btn').disabled = false;
		    document.getElementById('user').innerHTML = core.user;
		    document.getElementById('welcomeUser').style.display = 'none';
		    document.getElementById('userProfileBtn').style.display = 'none';
		    document.getElementById('userProfileBtn').disabled = true;
		    document.getElementById('signOutTool').style.display = 'none';
		    document.getElementById('roleTool').style.display = 'none';
		    userSpecificItems = document.getElementsByClassName('userSpecific');
		    for (var i = 0; i < userSpecificItems.length; i++) {
			elm = userSpecificItems[i];
			elm.parentNode.removeChild(elm);
		    }
		    document.getElementById('userTools').innerHTML = '';
		    if (document.getElementById('signInTool').children['username'].value == '')
			document.getElementById('signInTool').children['username'].focus();
		    else
			document.getElementById('signInTool').children['password'].focus();
		}
		core.hideAllChildElements('bodySpace');
		homeElms = core.body.getElementsByClassName('home');
		for (var i = 0; i < homeElms.length; i++) {
		    homeElms[i].style.display = null
		}
		return true;
		break;

	    case("registerationForm"):
		core.hideAllChildElements('bodySpace');
		document.getElementById('registerationFormPage').style.display = null;
		ctrller.disable = true;
		return true;
		break;

	    case("editProfileForm"):
		core.hideAllChildElements('bodySpace');
		document.getElementById('editProfileFormPage').style.display = null;
		return true;
		break;

	    case("userProfile"):
		core.hideAllChildElements('bodySpace');
		document.getElementById('userProfilePage').style.display = null;
		return true;
		break;

	    default :
		break;
	}
	return false;
    },
    iframeLoader: function(docName) {
	if (window.frames[docName + 'Frame'].document.getElementsByTagName('status')[0].firstChild.nodeValue == 'signedOut') {
	    core.clientSignOut();
	    core.statusField.innerHTML = '<span style="color:red">Session expired, plzz re-sign in :|</span>';
	}
	else {
	    var newHTMLWindow = window.frames[docName + 'Frame'];
	    var tool = document.getElementById(docName + 'Tool');
	    var displayArea = tool.children['displayArea'];
	    var iframeBody = window.frames[docName + 'Frame'].document.getElementById(docName);
	    window.frames[docName + 'Frame'].core = window.core;
	    displayArea.innerHTML = "<div id=\"MRC\"><button id=\"hide\" class=\"hideBtn\" onclick=\"this.parentElement.parentElement.style.display='none'; return false;\">Hide</button><button id=\"close\" class=\"closeBtn\" onclick=\"this.parentElement.parentElement.innerHTML='';return false;\">Close</button></div></div>";
	    displayArea.children['MRC'].style.position = 'absolute';
	    displayArea.children['MRC'].style.right = '0px';
	    displayArea.appendChild(iframeBody);
	    newHTMLWindow.initToolBox();
	}
    },
    keyHandler: function(evt) {
	evt = (evt) ? evt : ((window.event) ? window.event : null);
	if (evt) {
	    var srcElm = (evt.target ? evt.target : (evt.srcElement ? evt.srcElement : null));
	    if (srcElm.id == 'password') {
		if (evt.keyCode == 13) {
		    core.signIn(srcElm.parentElement.children['username'].value, srcElm.parentElement.children['password'].value);
		}
	    }
	    /*if(srcElm.id=='searchMouth'){
	     if(evt.keyCode==13){
	     searchBtnFunction(srcElm);
	     }
	     }*/
	}
    },
    search: function() {
	var displayArea = this.children['displayArea'];
	if (displayArea && (displayArea.childNodes.length < 1 || displayArea.style.display != 'none')) {
	    statusField.innerHTML = 'searching...';
	    this.gadgetLoader = new core.loadAsGadget2('lib/search.php', 'searchString=' + this.children['searchMouth'].value, displayArea);
	} else {
	    if (displayArea) {
		displayArea.style.display = null;
	    } else {
		this.dispArea.mrc.panelBtn.panel.dePanelizeBtn.onclick.call(this.dispArea.mrc.panelBtn.panel.dePanelizeBtn)
	    }
	}
	return false;
    },
    objectize: function() {
	var searchTool = document.getElementById('searchTool');
	if (searchTool.children['passKeyBox'].offsetHeight < 1) {
	    var passKeyBox = searchTool.children['passKeyBox'];
	    passKeyBox.style.display = 'inline';
	    this.style.backgroundPosition = "-167px -95px"
	    this.title = "Cancel";
	    searchTool.children['searchBtn'].style.backgroundPosition = "-215px 1px";
	    searchTool.children['searchBtn'].title = "Objectize";
	    if(searchTool.searchMouth.value=="Search")searchTool.searchMouth.value="Type-in ObjectId"
	    searchTool.children['searchMouth'].onblur.call(searchTool.children['searchMouth']);
	    searchTool.onsubmit = function() {
		var feed = new Object();
		feed.content = {
		    id: this.children['searchMouth'].value,
		    passKey: this.children['passKeyBox'].value
		}
		feed.elm = this;
		feed.postExpedition = function(feed) {
		    if (feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue == 'success') {
			statusField.innerHTML = 'U r objectized in ' + core.organization + ' ~:)~ welcome.';
			var np = feed.responseXML.getElementsByTagName('newpos')[0];
			feed.elm.onsubmit = core.search;
			feed.elm.children['searchBtn'].style.backgroundPosition = '-47px 1px';
			feed.elm.children['objectizeBtn'].style.backgroundPosition = '-215px 1px';
			feed.elm.removeChild(feed.elm.children['passKeyBox']);
			var prop = 'o' + np.getElementsByTagName('oid')[0].textContent;
			core.userInfo.positions[prop] = {};
			core.userInfo.positions[prop].label = np.getElementsByTagName('label')[0].textContent;
			core.userInfo.positions[prop].func = np.getElementsByTagName('func')[0].textContent;
			core.userInfo.positions[prop].aL = np.getElementsByTagName('al')[0].textContent;
			core.userInfo.positions[prop].oid = np.getElementsByTagName('oid')[0].textContent;
			core.userInfo.positions[prop].id = np.getElementsByTagName('id')[0].textContent;
			core.transit();
		    } else {
			statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
		    }
		}
		feed.ferry = new core.shuttle('./lib/adminScripts/objectize.php', feed.content, feed.postExpedition, feed);
	    }
	} else {
	    var pkb = searchTool.children['passKeyBox'];
	    pkb.style.display = 'none';
	    this.style.backgroundPosition = "-215px 1px";
	    this.title = "Objectize";
	    searchTool.children['searchBtn'].style.backgroundPosition = "-47px 1px";
	    searchTool.children['searchBtn'].title = "Search";
	    searchTool.children['searchMouth'].value = "";
	    searchTool.children['searchMouth'].onblur.call(searchTool.children['searchMouth']);
	    searchTool.onsubmit = core.search;
	}
	return false;
    },
    print: function() {
	var elm = this.parentElement;
	var newWin = window.open("");
	newWin.document.write("<script type='text/javascript'>printWin=true;</script>" + elm.outerHTML);
	newWin.print();
	newWin.close();
    },
    isElmsClass: function(elm, className) {
	for (i = 0; i < elm.classList.length; i++) {
	    if (elm.classList[i] == className)
		return true;
	}
	return false;
    },
    shuttle: function(url, content, postExpedition, feed) {
	if (this instanceof arguments.callee) {
	    if (feed.async == undefined)
		feed.async = true;
	    this.__proto__ = new XMLHttpRequest();
	    this.__proto__.open("POST", url, feed.async);
	    this.__proto__.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    if (feed.reqHeaders != undefined) {
		for (var i = 0; i < feed.reqHeaders.length; i++) {
		    this.__proto__.setRequestHeader(feed.reqHeaders[i][0], feed.reqHeaders[i][1]);
		}
	    }
	    if (feed.content != undefined && feed.content.__proto__ === new Object().__proto__) {
		content = [];
		feed.content.role = core.userInfo.role;
		feed.content.SESSION_ID = sessionId;
		for (var id in feed.content) {
		    content[content.length] = encodeURIComponent(id) + "=" + encodeURIComponent(feed.content[id]);
		}
		content = content.join('&');
	    }
	    if (feed.upldOnProgress) {
		this.upload.onprogress = feed.upldOnProgress;
	    }
	    this.__proto__.onreadystatechange = function() {
		if (this.readyState == 3) {
		    feed.responseText = this.responseText;
		    if (feed.onreceiving) {
			feed.onreceiving(feed);
		    }
		}
		if (this.status == 200 && this.readyState == 4) {
		    if (this.responseXML) {
			if (this.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue == 'signedOut') {
			    core.clientSignOut();
			} else {
			    if (feed) {
				feed.responseXML = this.responseXML;
			    }
			    postExpedition(feed);
			}
		    } else if (this.responseText) {
			if (this.responseText == 'signedOut') {
			    core.clientSignOut();
			}
			else {
			    feed.doc = document.createElement('div');
			    feed.doc.innerHTML = this.responseText;
			    feed.responseText = this.responseText;
			}
			postExpedition(feed);
		    }
		}
	    }
	    this.__proto__.send(content);
	} else
	    throw 'xhr object constructor cannot be called as a function';
    },
    msgPanel: function(name, parent, objOwner, body, icon) {
	var panel = document.createElement('div');
	panel.parent = parent;
	parent.appendChild(panel);
	panel.id = name + 'Panel';
	panel.className = 'msgPanel draggable';
	panel.closeBtn = new Image();
	panel.objOwner = objOwner
	panel.closeBtn.src = './images/x.png';
	panel.closeBtn.id = 'closeBtn';
	panel.closeBtn.style.position = 'absolute';
	panel.closeBtn.style.top = '4px';
	panel.closeBtn.style.right = '2px';
	panel.closeBtn.style.zIndex = 2;
	panel.closeBtn.title = 'close';
	panel.titleBar = document.createElement('div');
	panel.titleBar.className = 'holder';
	if (icon) {
	    panel.titleBar.appendChild(icon);
	}
	var title = document.createElement('span');
	if (name)
	    title.innerHTML = name + '&nbsp;&nbsp;&nbsp;&nbsp';
	else
	    title.innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp';
	panel.titleBar.appendChild(title);
	panel.titleBar.style.backgroundColor = '#6666C5';
	panel.titleBar.style.color = '#FFF';
	panel.appendChild(panel.titleBar);
	panel.body = body;
	body.classList.add('panelBody');
	panel.appendChild(panel.closeBtn);
	panel.appendChild(body);
	panel.titleBar.title = name;
	panel.activate = function() {
	    if (!panel.parentElement) {
		panel.parent.appendChild(this);
	    }
	    if (!this.classList.contains('active')) {
		this.style.display = null;
		var panels = document.body.getElementsByClassName('msgPanel');
		var maxZind = 0;
		for (var i = 0; i < panels.length; i++) {
		    maxZind = panels[i].style.zIndex > maxZind ? parseInt(panels[i].style.zIndex) : maxZind;
		    panels[i].titleBar.style.backgroundColor = '#9999dd';
		    panels[i].style.backgroundColor = '#BBF';
		    panels[i].classList.remove('active');
		}
		this.style.zIndex = maxZind < 20 ? 20 : (maxZind + 1);
		this.style.display = null;
		this.titleBar.style.backgroundColor = '#6666C5';
		this.style.backgroundColor = '#AAF';
		if (!this.classList.contains('1')) {
		    this.reAlign();
		    this.classList.add('1');
		}
		this.classList.add('active');
	    }
	}
	panel.reAlign = function() {
	    var msgPanels = document.body.getElementsByClassName('msgPanel');
	    this.body.style.height = null;
	    this.body.style.width = null;
	    if (this.body.offsetWidth > (window.innerWidth / 1.5)) {
		this.style.left = 100 + 19 * (msgPanels.length - 1) + 'px';
		this.body.style.width = window.innerWidth / 1.5 + 'px';
	    } else
		this.style.left = ((window.innerWidth - this.body.offsetWidth) / 2) + 19 * (msgPanels.length - 1) + 'px';
	    if (this.body.offsetHeight > (window.innerHeight / 1.5)) {
		this.style.top = 50 + 19 * (msgPanels.length - 1) + 'px';
		this.body.style.height = window.innerHeight / 1.5 + 'px';
	    } else {
		this.style.top = ((window.innerHeight - this.body.offsetHeight) / 2) + 19 * (msgPanels.length - 1) + 'px';
	    }
	}
	panel.closePanel = function() {
	    this.parentElement.removeChild(this);
	}
	panel.closeBtn.onclick = function() {
	    this.parentElement.parentElement.removeChild(this.parentElement);
	    event.cancelBubble = true;
	};
	panel.addEventListener('click', panel.activate, true);
	panel.activate();
	panel.focus();
	core.makeDraggable(panel, document.body);
	return panel;
    },
    moveObject: function(evt) {
    },
    loadUserTools: function() {
	var feed = new Object();
	var displayUserTools = function(feed) {
	    var tools = feed.doc.getElementsByClassName('tool');
	    var userTools = document.getElementById('userTools');
	    userTools.innerHTML = "";
	    for (var i = 0; i < tools.length; i++) {
		var tool = tools[i].cloneNode(true);
		var scs = tool.getElementsByTagName('script');
		var j = 0;
		while (j < scs.length) {
		    scs[j].parentElement.replaceChild(core.cloneNode(scs[j]), scs[j]);
		    j++;
		}
		userTools.appendChild(tool);
	    }
	}
	feed.content = {};
	feed.ferry = new core.shuttle('lib/userTools.php', '', displayUserTools, feed);
    },
    uploadFile: function(infoBox) {
	var file = this.files[0];
	var feed = new Object();
	var fileName = document.createElement('span');
	fileName.id = file.name;
	fileName.innerHTML = file.name;
	infoBox.appendChild(fileName);
	var pInd = new Image();
	pInd.src = 'images/loading.gif';
	pInd.id = 'pInd';
	feed.pInd = pInd;
	var gauge = document.createElement('span');
	gauge.id = 'gauge';
	feed.elm = this;
	feed.upldOnProgress = function(e) {
	    if (e.lengthComputable) {
		gauge.innerHTML = '(' + parseInt(e.loaded / e.total * 100) + '%)';
	    }
	}
	feed.postExpedition = function(feed) {
	    feed.response = null;
	    try {
		feed.response = eval("(" + feed.responseText + ")");
	    } catch (err) {
		feed.response = {};
	    }
	    if (feed.response.success) {
		feed.pInd.parentElement.removeChild(feed.pInd);
		feed.elm.postUpload(feed);
	    } else if (feed.response.error) {
		feed.pInd.src = '/images/x.png';
		feed.pInd.parentElement.children['gauge'].innerHTML = '';
		statusField.innerHTML = feed.response.error;
	    }
	}
	feed.reqHeaders = [["X-Requested-With", "XMLHttpRequest"], ["X-File-Name", encodeURIComponent(name)], ["Content-Type", "application/octet-stream"]];
	feed.ferry = new core.shuttle("lib/superScripts/uploadProfilePic.php?op=usrPic&qqfile=" + encodeURIComponent(file.fileName), file, feed.postExpedition, feed);
	fileName.insertAdjacentElement('beforeBegin', document.createElement('br'));
	fileName.insertAdjacentElement('beforeBegin', pInd);
	fileName.insertAdjacentElement('afterEnd', gauge);
    },
    xmlToHTML: function(elm, parentElement) {
	var hElm = elm.cloneNode();
	if (elm.tagName) {
	    hElm = document.createElement(elm.tagName);
	    for (var i = 0; i < elm.attributes.length; i++) {
		hElm.setAttribute(elm.attributes[i].localName, elm.attributes[i].value);
	    }
	    parentElement.appendChild(hElm);
	    for (var i = 0; i < elm.childNodes.length; i++) {
		new core.xmlToHTML(elm.childNodes[i], hElm);
	    }
	} else {
	    parentElement.appendChild(hElm);
	}
	return this;
    },
    loadAsGadget: function(url, content, dispArea, name) {
	window.initGadget = null;
	var feed = new Object();
	feed.url = url;
	feed.dispArea = dispArea;
	name = name ? name : '';
	dispArea.name = name;
	dispArea.parent = dispArea.parentElement;
	dispArea.parent[name + 'dispArea'] = dispArea;
	feed.postExpedition = function(feed) {
	    var links = feed.doc.getElementsByTagName('link');
	    var csss = [];
	    for (var i = 0; i < links.length; i++) {
		if (links[i].attributes['rel'].value == 'stylesheet') {
		    csss[csss.length] = links[i];
		}
	    }
	    for (var i = 0; i < csss.length; i++) {
		csss[i].attributes['href'].value = core.pathBuilder(csss[i].attributes['href'].value, feed.url);
		dispArea.appendChild(csss[i])
	    }
	    var displayElms = feed.doc.getElementsByClassName('display');
	    for (var j = 0; j < displayElms.length; ) {
		dispArea.appendChild(displayElms[j])
	    }
	    var scipts = feed.doc.getElementsByClassName('gadgetScript');
	    var scripts = [];
	    for (var i = 0; i < scipts.length; i++) {
		scripts[scripts.length] = scipts[i];
	    }
	    var winScripts = [];
	    var windowSc = document.getElementsByTagName('script');
	    for (var i = 0; i < windowSc.length; i++) {
		if (windowSc[i].hasAttribute('src')) {
		    winScripts[winScripts.length] = windowSc[i].attributes['src'].value;
		}
	    }
	    if (scripts.length > 0)
		feed.scriptLoader = new core.loadScripts(scripts, winScripts, gadgetMsg, feed.url, dispArea, afterLoadScript);
	}
	feed.content = content + '&gadget=true';
	feed.ferry = new core.shuttle(url, feed.content, feed.postExpedition, feed);
    },
    loadAsGadget2: function(url, content, dispArea, name) {
	window.initGadget = null;
	var feed = new Object();
	feed.url = url;
	feed.dispArea = dispArea;
	name = name ? name : '';
	dispArea.name = name;
	dispArea.parent = dispArea.parentElement;
	dispArea.parent[name + 'dispArea'] = dispArea;
	feed.postExpedition = function(feed) {
	    feed.dispArea.innerHTML = '';
	    var gdgBdy = feed.doc.getElementsByClassName('gdgBody')[0].cloneNode(true);
	    var scs = gdgBdy.getElementsByTagName('script');
	    var j = 0;
	    var ascs = [];
	    for (var i = 0; i < scs.length; i++) {
		ascs.push(scs[i]);
	    }
	    while (j < ascs.length) {
		scs[j].parentElement.replaceChild(core.cloneNode(scs[j]), scs[j]);
		j++;
	    }
	    feed.dispArea.appendChild(gdgBdy);
	    feed.dispArea.mrc = new core.mrc();
	    feed.dispArea.mrc.panelBtn = feed.dispArea.mrc.children['panelBtn'];
	    feed.dispArea.mrc.panelBtn.body = feed.dispArea;
	    feed.dispArea.mrc.panelBtn.onclick = core.panelize;
	    feed.dispArea.mrc.style.position = 'absolute';
	    feed.dispArea.mrc.style.right = '0px';
	    feed.dispArea.mrc.style.top = '0px';
	    feed.dispArea.appendChild(feed.dispArea.mrc);
	}
	feed.content = content || {};
	feed.content.gadget = 'true';
	feed.ferry = new core.shuttle(url, feed.content, feed.postExpedition, feed);
    },
    loadScripts: function(scripts, winScripts, gadgetMsg, url, dispArea, afterLoadScript) {
	this.dispArea = dispArea;
	if (scripts[0].hasAttribute('src')) {
	    var uReLo = core.pathBuilder(scripts[0].attributes['src'].value, url);
	    var scriptExists = false;
	    for (var j = 0; j < winScripts.length; j++) {
		if (uReLo == winScripts[j]) {
		    scriptExists = true;
		    break;
		}
	    }
	    if (!scriptExists) {
		var sc = scripts[0].cloneNode();
		sc.setAttribute('src', uReLo);
		sc.innerHTML = scripts[0].innerHTML;
		if (scripts.length > 1) {
		    scripts = scripts.splice(1);
		    sc.onload = function() {
			this.scriptLoader = new core.loadScripts(scripts, winScripts, gadgetMsg, url, dispArea, afterLoadScript);
			return false;
		    }
		} else {
		    sc.onload() = function() {
			initGadget(dispArea);
			core.statusField.innerHTML = gadgetMsg;
			if (afterLoadScript)
			    afterLoadScript();
		    }
		}
		document.head.appendChild(sc);
	    } else {
		if (scripts.length > 1) {
		    scripts = scripts.splice(1);
		    this.scriptLoader = new core.loadScripts(scripts, winScripts, gadgetMsg, url, dispArea, afterLoadScript);
		} else {
		    initGadget(dispArea);
		    core.statusField.innerHTML = gadgetMsg;
		    if (afterLoadScript)
			afterLoadScript();
		}
	    }
	} else {
	    var sc = scripts[0].cloneNode();
	    sc.innerHTML = scripts[0].innerHTML;
	    dispArea.appendChild(sc);
	    if (scripts.length > 1) {
		scripts = scripts.splice(1);
		this.scriptLoader = new core.loadScripts(scripts, winScripts, gadgetMsg, url, dispArea, afterLoadScript);
	    } else {
		initGadget(dispArea);
		core.statusField.innerHTML = gadgetMsg;
		if (afterLoadScript)
		    afterLoadScript();
	    }
	}
    },
    pathBuilder: function(child, root) {
	if (root[root.length - 1] != '/') {
	    var i = root.length - 1;
	    do {
		i--;
	    } while (root[i] != '/' && i != -1);
	    root = root.slice(0, i + 1);
	}
	if (child[0] == '.' && child[1] == '/') {
	    child = child.slice(2);
	}
	if (child[0] != '.') {
	    return root + child;
	} else if (child.slice(0, 3) == '../') {
	    while (child.slice(0, 3) == '../') {
		child = child.slice(3);
		var i = root.length - 1;
		do {
		    i--;
		} while (root[i] != '/' && i != -1);
		root = root.slice(0, i + 1);
	    }
	    return root + child
	}
	return root + child;
    },
    selectAll: function() {
	var elm = this;
	if (elm.click) {
	    elm.dblclick = true;
	} else {
	    elm.click = true;
	    core.globalVar = elm;
	    var dblclicker = setTimeout('core.selector()', 500);
	}
	return false;
    },
    selector: function() {
	var elm = core.globalVar;
	core.globalVar = null;
	elm.click = false;
	if (elm.classList.contains('all')) {
	    var classes = [];
	    for (var i = 0; i < elm.classList.length; i++) {
		if (elm.classList.item(i) != 'all') {
		    classes[classes.length] = elm.classList.item(i)
		}
	    }
	    classes = classes.join(' ');
	    var elms = document.getElementsByClassName(classes);
	}
	if (elm.dblclick) {
	    if (!elm.allSelected) {
		for (i = 0; i < elms.length; i++) {
		    elms[i].checked = true;
		}
		elm.selected = true;
		elm.allSelected = true;
		elm.style.border = '1px solid black';
	    } else {
		for (i = 0; i < elms.length; i++) {
		    elms[i].checked = false;
		}
		elm.selected = false;
		elm.allSelected = false;
		elm.style.border = '1px solid transparent';
	    }
	    if (elm.selected) {
		elm.mState = 1;
		elm.anim(1 + 3 * elm.mState);
	    } else {
		elm.mState = 0;
		elm.anim(1 + 3 * elm.mState);
	    }
	} else {
	    if (!elm.selected) {
		for (i = 0; i < elms.length; i++) {
		    if (elms[i].offsetHeight != 0)
			elms[i].checked = true;
		}
		elm.selected = true;
	    } else {
		for (i = 0; i < elms.length; i++) {
		    if (elms[i].offsetHeight != 0)
			elms[i].checked = false;
		}
		elm.allSelected = false;
		elm.selected = false;
		if (elm.style.border == '1px solid black')
		    elm.style.border = '1px solid yellow';
	    }
	}
	elm.dblclick = false;
    },
    animatedImage: function(srcBnk, clickFunc, proto) {
	this.__proto__ = (proto ? proto : new Image());
	for (i = 0; i < srcBnk.length; i++) {
	    this.__proto__.src = srcBnk[i]
	}
	this.__proto__.src = srcBnk[0];
	this.__proto__.style = null;
	this.__proto__.srcBnk = srcBnk;
	this.__proto__.mStates = srcBnk.length / 3;
	this.__proto__.mState = 0;
	this.__proto__.anim = function(serial) {
	    if (this.srcBnk[serial])
		this.src = this.srcBnk[serial];
	}
	this.trigMouseEvt = function() {
	    this.onmouseout = function() {
		this.anim(0 + 3 * this.mState);
	    }
	    this.onmouseover = function() {
		this.anim(1 + 3 * this.mState);
	    }
	    this.onmousedown = function() {
		this.anim(2 + 3 * this.mState);
	    }
	    this.onmouseup = function() {
		if (!this.disabled) {
		    if (clickFunc)
			clickFunc.call(this);
		    this.mState = (this.mState + 1) % this.mStates;
		    this.anim(1 + 3 * this.mState);
		} else {
		    this.anim(1 + 3 * this.mState);
		}
	    }
	}
    },
    logStatus: function() {
	statusField.removeEventListener('DOMSubtreeModified', arguments.callee, false);
	statusField.count++;
	var lB = document.createElement('div');
	lB.id = statusField.count;
	lB.className = 'logMsg';
	lB.innerHTML = this.innerHTML;
	logPanel.body.appendChild(lB);
	if (logPanel.offsetWidth > innerWidth * 0.9)
	    logPanel.style.width = innerWidth * 0.9 + "px";
	if (logPanel.offsetHeight > innerHeight * 0.9)
	    logPanel.style.height = innerHeight * 0.9 + "px";
	logPanel.reAlign();
	lB.scrollIntoViewIfNeeded();
	statusField.innerHTML = core.expressText(statusField.innerHTML);
	statusField.addEventListener('DOMSubtreeModified', arguments.callee, false);
    },
    makeDraggable: function(div, area) {
	div.style.position = 'fixed';
	var engageDrag = function() {
	    if (this.dragDiv.activate)
		this.dragDiv.activate();
	    this.dragDiv.engageDrag = true;
	    this.dragDiv.dragArea.dragDiv = this.dragDiv;
	    this.dragDiv.dragArea.onmousemove = this.onmousemove;
	    this.dragDiv.dragArea.onmouseup = this.onmouseup;
	    this.dragDiv.preX = event.clientX;
	    this.dragDiv.preY = event.clientY;
	    event.cancelBubble = true;
	    event.preventDefault();
	}
	var drag = function() {
	    if (this.dragDiv.engageDrag && this.dragDiv.offsetTop >= this.dragDiv.dragArea.offsetTop /*&& this.dragDiv.offsetLeft >= this.dragDiv.dragArea.offsetLeft*/) {
		this.dragDiv.style.top = (this.dragDiv.offsetTop + (event.clientY - this.dragDiv.preY)) + 'px';
		this.dragDiv.preY = event.clientY;
		this.dragDiv.style.left = (this.dragDiv.offsetLeft + (event.clientX - this.dragDiv.preX)) + 'px';
		this.dragDiv.preX = event.clientX;
		event.cancelBubble = true;
		event.preventDefault();
	    } else if (this.dragDiv.engageDrag && this.dragDiv.offsetTop < this.dragDiv.dragArea.offsetTop) {
		this.dragDiv.style.top = this.dragDiv.dragArea.offsetTop + 'px';
		this.dragDiv.preY = event.clientY;
		this.dragDiv.style.left = (this.dragDiv.offsetLeft + (event.clientX - this.dragDiv.preX)) + 'px';
		this.dragDiv.preX = event.clientX;
		event.cancelBubble = true;
		event.preventDefault();
	    } else if (this.dragDiv.offsetLeft < this.dragDiv.dragArea.offsetLeft) {
		//this.dragDiv.style.left=this.dragDiv.dragArea.offsetLeft+'px';
	    }
	}
	var release = function() {
	    this.dragDiv.engageDrag = false;
	    this.dragDiv.dragArea.onmousemove = null;
	    this.dragDiv.dragArea.onmouseup = null;
	    this.dragDiv.dragArea.dragDiv = null;
	    event.cancelBubble = true;
	    event.preventDefault();
	}
	var holders = div.getElementsByClassName('holder');
	for (i = 0; i < holders.length; i++) {
	    holders[i].dragDiv = div;
	    holders[i].dragDiv.dragArea = area;
	    holders[i].onmousedown = engageDrag;
	    holders[i].onmousemove = drag;
	    holders[i].onmouseup = release;
	}
	div.dragDiv = div;
	div.dragArea = area;
    },
    scrollOnDragToEdge: function(timer) {
	if (event.clientY >= window.innerHeight - 60 && core.mbody.scrollTop < core.mbody.scrollHeight + 16 - innerHeight) {
	    core.mbody.scrollTop = core.mbody.scrollTop + 20;
	    if (timer) {
		if (!core.scrollOnDragToEdge.rt) {
		    core.scrollOnDragToEdge.lrt = true;
		    core.scrollOnDragToEdge.rt = true;
		    core.scrollOnDragToEdge.rightTimer = window.setInterval(function() {
			core.mbody.scrollTop = core.mbody.scrollTop + 20;
		    }, 100);
		    core.body.addEventListener('mouseup', function() {
			window.clearInterval(core.scrollOnDragToEdge.rightTimer);
			core.body.removeEventListener('mouseup', arguments.callee, false);
		    }, false);
		}
	    }
	} else if (event.clientY <= 138 && core.mbody.scrollTop > 0) {
	    core.mbody.scrollTop = core.mbody.scrollTop - 20;
	    if (timer) {
		if (!core.scrollOnDragToEdge.lt) {
		    core.scrollOnDragToEdge.lrt = true;
		    core.scrollOnDragToEdge.lt = true;
		    core.scrollOnDragToEdge.leftTimer = window.setInterval(function() {
			core.mbody.scrollTop = core.mbody.scrollTop - 20;
		    }, 100);
		    core.body.addEventListener('mouseup', function() {
			window.clearInterval(core.scrollOnDragToEdge.leftTimer);
			core.body.removeEventListener('mouseup', arguments.callee, false);
		    }, false);
		}
	    }
	} else {
	    if (core.scrollOnDragToEdge.lrt) {
		core.scrollOnDragToEdge.lrt = false;
		core.scrollOnDragToEdge.lt = false;
		core.scrollOnDragToEdge.rt = false;
		window.clearInterval(core.scrollOnDragToEdge.leftTimer);
		window.clearInterval(core.scrollOnDragToEdge.rightTimer);
	    }
	}
	if (event.clientX >= window.innerWidth - 60 && core.mbody.scrollLeft < core.mbody.scrollWidth + 16 - innerWidth) {
	    core.mbody.scrollLeft = core.mbody.scrollLeft + 20;
	    if (timer) {
		if (!core.scrollOnDragToEdge.dt) {
		    core.scrollOnDragToEdge.udt = true;
		    core.scrollOnDragToEdge.dt = true;
		    core.scrollOnDragToEdge.downTimer = window.setInterval(function() {
			core.mbody.scrollLeft = core.mbody.scrollLeft + 20;
		    }, 100);
		    core.body.addEventListener('mouseup', function() {
			window.clearInterval(core.scrollOnDragToEdge.downTimer);
			core.body.removeEventListener('mouseup', arguments.callee, false);
		    }, false);
		}
	    }
	} else if (event.clientX <= 60 && core.mbody.scrollLeft > 0) {
	    core.mbody.scrollLeft = core.mbody.scrollLeft - 20;
	    if (timer) {
		if (!core.scrollOnDragToEdge.ut) {
		    core.scrollOnDragToEdge.udt = true;
		    core.scrollOnDragToEdge.ut = true;
		    core.scrollOnDragToEdge.upTimer = window.setInterval(function() {
			core.mbody.scrollLeft = core.mbody.scrollLeft - 20;
		    }, 100);
		    core.body.addEventListener('mouseup', function() {
			window.clearInterval(core.scrollOnDragToEdge.upTimer);
			core.body.removeEventListener('mouseup', arguments.callee, false);
		    }, false);
		}
	    }
	} else {
	    if (core.scrollOnDragToEdge.udt) {
		core.scrollOnDragToEdge.udt = false;
		core.scrollOnDragToEdge.ut = false;
		core.scrollOnDragToEdge.dt = false;
		window.clearInterval(core.scrollOnDragToEdge.upTimer);
		window.clearInterval(core.scrollOnDragToEdge.downTimer);
	    }
	}
    },
    selectOnDrag: function() {
	if (!selectRectangle.selecting && event.button === 0) {
	    selectRectangle.lx = event.clientX + core.mbody.scrollLeft - 8;
	    selectRectangle.ty = event.clientY + core.mbody.scrollTop - 78;
	    selectRectangle.rx = core.body.offsetWidth - selectRectangle.lx - 1;
	    selectRectangle.by = core.body.offsetHeight - selectRectangle.ty - 1;
	    selectRectangle.ix = event.clientX;
	    selectRectangle.iy = event.clientY;
	    selectRectangle.style.left = selectRectangle.lx + 'px';
	    selectRectangle.style.top = selectRectangle.ty + 'px';
	    selectRectangle.style.width = '0px';
	    selectRectangle.style.height = '0px';
	    selectRectangle.style.opacity = '0.2';
	    selectRectangle.style.display = 'none';
	    selectRectangle.selectStart = true;
	    selectedElements = new core.nodeList();
	    selectedElements.push(document.elementFromPoint(event.clientX, event.clientY));
	    core.body.addEventListener('mousemove', function() {
		if (selectRectangle.selecting || selectRectangle.selectStart && (selectRectangle.ix != event.clientX || selectRectangle.iy != event.clientY)) {
		    if (selectRectangle.selectStart) {
			selectRectangle.selectStart = false;
			selectRectangle.selecting = true;
			selectRectangle.style.display = null;
		    }
		    if (event.button === 0) {
			core.scrollOnDragToEdge(true);
			if (event.clientX + core.mbody.scrollLeft - 8 < selectRectangle.lx) {
			    selectRectangle.style.left = null;
			    selectRectangle.style.right = selectRectangle.rx + 'px';
			    selectRectangle.style.width = (selectRectangle.lx - (event.clientX + core.mbody.scrollLeft - 8)) + 'px';
			} else {
			    selectRectangle.style.right = null;
			    selectRectangle.style.left = selectRectangle.lx + 'px';
			    selectRectangle.style.width = ((event.clientX + core.mbody.scrollLeft - 8) - selectRectangle.lx) + 'px';
			}
			if (event.clientY + core.mbody.scrollTop - 78 < selectRectangle.ty) {
			    selectRectangle.style.top = null;
			    selectRectangle.style.bottom = selectRectangle.by + 'px';
			    selectRectangle.style.height = (selectRectangle.ty - (event.clientY + core.mbody.scrollTop - 78)) + 'px';
			} else {
			    selectRectangle.style.bottom = null;
			    selectRectangle.style.top = selectRectangle.ty + 'px';
			    selectRectangle.style.height = ((event.clientY + core.mbody.scrollTop - 78) - selectRectangle.ty) + 'px';
			}
		    } else {
			core.collectElements();
		    }
		} else {
		    this.removeEventListener('mousemove', arguments.callee, false);
		}
	    }, false);
	    document.body.addEventListener('mouseup', core.collectElements, true);
	} else if (!selectRectangle.selecting && event.button == 2) {
	    selectedElements = new core.nodeList();
	    selectedElements.push(document.elementFromPoint(event.clientX, event.clientY));
	}
    },
    nodeList: function() {
	this.__proto__ = [];
	this.__proto__.getElementsByClassName = function(className) {
	    var classes = className.split(' ');
	    var csa = [];
	    var mcc = function(classes, elm) {
		for (var i = 0; i < classes.length; i++) {
		    if (!elm.classList.contains(classes[i])) {
			return false;
		    }
		}
		return true;
	    }
	    var sl = selectedElements.length;
	    for (var i = 0; i < sl; i++) {
		if (mcc(classes, selectedElements[i])) {
		    csa.push(selectedElements[i]);
		}
	    }
	    return csa;
	}
	this.__proto__.getElementsByTagName = function(tagName) {
	    var tsa = [];
	    var sl = selectedElements.length;
	    for (var i = 0; i < sl; i++) {
		if (selectedElements[i].tagName.toLowerCase() == tagName) {
		    tsa.push(selectedElements[i]);
		}
	    }
	    return tsa;
	}
    },
    collectElements: function() {
	if (selectRectangle.selecting) {
	    selectRectangle.selecting = false;
	    var sx = selectRectangle.offsetLeft + 8 - core.mbody.scrollLeft;
	    var sy = selectRectangle.offsetTop + 78 - core.mbody.scrollTop;
	    var ex = sx + selectRectangle.offsetWidth;
	    var ey = sy + selectRectangle.offsetHeight;
	    var x = sx, y = sy;
	    if (x < 8) {
		var psl = core.mbody.scrollLeft;
		core.mbody.scrollLeft = selectRectangle.offsetLeft;
		x = 8;
	    }
	    if (sy < 78) {
		var pst = core.mbody.scrollTop;
		core.mbody.scrollTop = core.mbody.scrollTop + sy - 78;
		y = y - (core.mbody.scrollTop - pst);
	    }
	    var isl = core.mbody.scrollLeft;
	    var ix = x;
	    if (sx != ex || sy != ey) {
		selectRectangle.style.display = 'none';
		var jok = false, iok = false;
		for (var j = sy; j <= ey; j += 10) {
		    iok = false;
		    for (var i = sx; i <= ex; i += 10) {
			if (x < 8) {
			    core.mbody.scrollLeft = isl;
			    x = ix
			}
			if (x > innerWidth - 8) {
			    var psl = core.mbody.scrollLeft;
			    core.mbody.scrollLeft = core.mbody.scrollLeft + x - 8;
			    x = x - (core.mbody.scrollLeft - psl)
			}
			if (y > innerHeight - 8) {
			    var pst = core.mbody.scrollTop;
			    core.mbody.scrollTop = core.mbody.scrollTop + y - 78;
			    y = y - (core.mbody.scrollTop - pst);
			}
			var se = document.elementFromPoint(x, y);
			var sel = selectedElements.length;
			var match = false;
			for (var n = 0; n < sel; n++) {
			    if (selectedElements[n] == se) {
				match = true;
			    }
			}
			if (!match) {
			    selectedElements.push(se);
			}
			x += 10;
			if (i + 10 > ex && !iok) {
			    x = x - (i + 10 - ex);
			    i = ex;
			    iok = true;
			}
		    }
		    x = sx;
		    y += 10;
		    if (j + 10 > ey && !jok) {
			y = y - (j + 10 - ey);
			j = ey;
			jok = true;
		    }
		}
		selectRectangle.style.display = null;
		selectRectangle.style.opacity = 0.4;
	    }
	}
	document.body.removeEventListener('mouseup', arguments.callee, true);
    },
    absPosition: function(elm) {
	var op = elm.offsetParent;
	var ol = elm.offsetLeft;
	var ot = elm.offsetTop;
	while (op) {
	    ol += op.offsetLeft;
	    ot += ot.offsetTop;
	    op = op.offsetParent;
	}
	return {
	    x: ol,
	    y: ot
	};
    },
    makeid: function(length) {
	var text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for (var i = 0; i < length; i++)
	    text += possible.charAt(Math.floor(Math.random() * possible.length));

	return text;
    },
    addContextMenu: function(elm, ecmenu) {
	if (ecmenu.parentElement)
	    ecmenu.parentElement.removeChild(ecmenu);
	elm.cmenu = ecmenu;
	cmenu.addItems = function() {
	    if (!event)
		var event = arguments[0];
	    event.preventDefault();
	    if (this == selectRectangle) {
		cmenuEvent = document.createEvent('MouseEvents');
		cmenuEvent.initMouseEvent('contextmenu', true, true, window, 1, event.screenX, event.screenY, event.clientX, event.clientY, false, false, false, false, 2, null);
		selectedElements[0].dispatchEvent(cmenuEvent);
		event.cancelBubble = true;
		return false;
	    }
	    if (event.srcElement == this)
		cmenu.mother = this;
	    cmenu.insertAdjacentElement('afterBegin', this.cmenu);
	    this.cmenu.style.display = null;
	    if (this == document.body || this.burstCMBubble) {
		cmenu.style.position = 'absolute';
		if ((innerWidth - 8 - (core.mbody.offsetWidth - core.mbody.clientWidth)) > (event.clientX + cmenu.offsetWidth)) {
		    cmenu.style.left = event.clientX + core.mbody.scrollLeft - 8 + 'px';
		} else {
		    cmenu.style.left = null;
		    cmenu.style.right = core.body.offsetWidth - (event.clientX + core.mbody.scrollLeft - 8) - 1 + 'px';
		}
		if ((innerHeight - 8 - (core.mbody.offsetHeight - core.mbody.clientHeight)) > (event.clientY + cmenu.offsetHeight)) {
		    cmenu.style.top = event.clientY + core.mbody.scrollTop - 8 + 'px';
		} else {
		    cmenu.style.top = null;
		    cmenu.style.bottom = core.body.offsetHeight - (event.clientY + core.mbody.scrollTop - 8) - 1 + 'px';
		}
		if (this.burstCMBubble)
		    event.cancelBubble = true;
		cmenu.style.display = null;
		cmenu.focus();
	    }
	}
	document.body.customCMenuElms.push(elm);
	elm.cmenuOpener = cmenu.addItems;
	elm.addEventListener('contextmenu', elm.cmenuOpener, false);
	return ecmenu;
    },
    defaultCMenu: function() {
	for (var i = 0; i < document.body.customCMenuElms.length; i++) {
	    document.body.customCMenuElms[i].removeEventListener('contextmenu', document.body.customCMenuElms[i].cmenuOpener, false);
	}
	document.body.removeEventListener('selectstart', document.body.selectStartFunc, false);
	core.body.removeEventListener('mousedown', core.selectOnDrag, false);
	core.body.removeEventListener('click', core.body.clickFunc, false);
	window.setTimeout(function() {
	    for (var i = 0; i < document.body.customCMenuElms.length; i++) {
		document.body.customCMenuElms[i].addEventListener('contextmenu', document.body.customCMenuElms[i].cmenuOpener, false);
	    }
	    document.body.addEventListener('selectstart', document.body.selectStartFunc, false);
	    core.body.addEventListener('mousedown', core.selectOnDrag, false);
	    core.body.addEventListener('click', core.body.clickFunc, false);
	}, this.children['time'].value * 1000);
	cmenu.style.display = 'none';
    },
    copyText: function() {
	var text;
	for (var i = 0; i < selectedElements.length; i++) {
	    text += selectedElements[i].textContent;
	}
	clipBoard.innerHTML = text;
	clipBoard.ex
    },
    popClipBoard: function(action) {
	if (action) {
	    cbh.style.left = (innerWidth - 200) / 2 + 'px';
	    cbh.style.top = (innerHeight - 100) / 2 + 'px';
	    if (action == 'copy') {
		clipBoard.opType = 'copy';
		cbh.firstChild.nodeValue = 'Choose Copy in context menu of this box or Ctrl+c.';
	    } else if (action == 'paste') {
		clipBoard.opType = 'paste';
		cbh.firstChild.nodeValue = 'Choose Paste in context menu of this box or Ctrl+v.';
	    }
	    cbh.style.display = null;
	    clipBoard.focus();
	}
    },
    selectElementContents: function(el) {
	var range = document.createRange();
	range.selectNodeContents(el);
	var sel = window.getSelection();
	sel.removeAllRanges();
	sel.addRange(range);
    },
    clipBoardAction: function() {
	if (clipBoard.opType == 'paste') {
	    clipBoard.afterPaste();
	    clipBoard.opType = 'pasteDone';
	}
    },
    mrc: function() {
	if (this instanceof arguments.callee) {
	    this.__proto__ = document.createElement('div');
	    this.id = "MRC";
	    this.className = "MRC"
	    this.innerHTML = "<img id=\"hide\" class=\"hideBtn\" onclick=\"this.parentElement.parentElement.style.display='none'; return false;\" src='images/-.png'/>&nbsp;<img id=\"panelBtn\" class=\"panelBtn\" src='images/wnd.png'/>&nbsp;<img id=\"close\" class=\"closeBtn\" onclick=\"this.parentElement.parentElement.style.height=null; this.parentElement.parentElement.style.width=null;this.parentElement.parentElement.innerHTML='';return false;\" src='images/x.png'/>";
	    return this.__proto__;
	}
	else {
	    throw 'xhr object constructor cannot be called as a function';
	}

    },
    objPropCount: function(obj) {
	if (obj.__proto__ == {}.__proto__) {
	    var count = 0;
	    for (var i in obj) {
		count++;
	    }
	    return count;
	}
    },
    MD5: function(string) {

	function RotateLeft(lValue, iShiftBits) {
	    return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
	}

	function AddUnsigned(lX, lY) {
	    var lX4, lY4, lX8, lY8, lResult;
	    lX8 = (lX & 0x80000000);
	    lY8 = (lY & 0x80000000);
	    lX4 = (lX & 0x40000000);
	    lY4 = (lY & 0x40000000);
	    lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
	    if (lX4 & lY4) {
		return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
	    }
	    if (lX4 | lY4) {
		if (lResult & 0x40000000) {
		    return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
		} else {
		    return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
		}
	    } else {
		return (lResult ^ lX8 ^ lY8);
	    }
	}

	function F(x, y, z) {
	    return (x & y) | ((~x) & z);
	}
	function G(x, y, z) {
	    return (x & z) | (y & (~z));
	}
	function H(x, y, z) {
	    return (x ^ y ^ z);
	}
	function I(x, y, z) {
	    return (y ^ (x | (~z)));
	}

	function FF(a, b, c, d, x, s, ac) {
	    a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
	    return AddUnsigned(RotateLeft(a, s), b);
	}
	;

	function GG(a, b, c, d, x, s, ac) {
	    a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
	    return AddUnsigned(RotateLeft(a, s), b);
	}
	;

	function HH(a, b, c, d, x, s, ac) {
	    a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
	    return AddUnsigned(RotateLeft(a, s), b);
	}
	;

	function II(a, b, c, d, x, s, ac) {
	    a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
	    return AddUnsigned(RotateLeft(a, s), b);
	}
	;

	function ConvertToWordArray(string) {
	    var lWordCount;
	    var lMessageLength = string.length;
	    var lNumberOfWords_temp1 = lMessageLength + 8;
	    var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
	    var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
	    var lWordArray = Array(lNumberOfWords - 1);
	    var lBytePosition = 0;
	    var lByteCount = 0;
	    while (lByteCount < lMessageLength) {
		lWordCount = (lByteCount - (lByteCount % 4)) / 4;
		lBytePosition = (lByteCount % 4) * 8;
		lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
		lByteCount++;
	    }
	    lWordCount = (lByteCount - (lByteCount % 4)) / 4;
	    lBytePosition = (lByteCount % 4) * 8;
	    lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
	    lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
	    lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
	    return lWordArray;
	}
	;

	function WordToHex(lValue) {
	    var WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
	    for (lCount = 0; lCount <= 3; lCount++) {
		lByte = (lValue >>> (lCount * 8)) & 255;
		WordToHexValue_temp = "0" + lByte.toString(16);
		WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
	    }
	    return WordToHexValue;
	}
	;

	function Utf8Encode(string) {
	    string = string.replace(/\r\n/g, "\n");
	    var utftext = "";

	    for (var n = 0; n < string.length; n++) {

		var c = string.charCodeAt(n);

		if (c < 128) {
		    utftext += String.fromCharCode(c);
		}
		else if ((c > 127) && (c < 2048)) {
		    utftext += String.fromCharCode((c >> 6) | 192);
		    utftext += String.fromCharCode((c & 63) | 128);
		}
		else {
		    utftext += String.fromCharCode((c >> 12) | 224);
		    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
		    utftext += String.fromCharCode((c & 63) | 128);
		}

	    }

	    return utftext;
	}
	;

	var x = Array();
	var k, AA, BB, CC, DD, a, b, c, d;
	var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
	var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
	var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
	var S41 = 6, S42 = 10, S43 = 15, S44 = 21;

	string = Utf8Encode(string);

	x = ConvertToWordArray(string);

	a = 0x67452301;
	b = 0xEFCDAB89;
	c = 0x98BADCFE;
	d = 0x10325476;

	for (k = 0; k < x.length; k += 16) {
	    AA = a;
	    BB = b;
	    CC = c;
	    DD = d;
	    a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
	    d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
	    c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
	    b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
	    a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
	    d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
	    c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
	    b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
	    a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
	    d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
	    c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
	    b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
	    a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
	    d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
	    c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
	    b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
	    a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
	    d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
	    c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
	    b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
	    a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
	    d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
	    c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
	    b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
	    a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
	    d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
	    c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
	    b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
	    a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
	    d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
	    c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
	    b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
	    a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
	    d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
	    c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
	    b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
	    a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
	    d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
	    c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
	    b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
	    a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
	    d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
	    c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
	    b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
	    a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
	    d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
	    c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
	    b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
	    a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
	    d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
	    c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
	    b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
	    a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
	    d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
	    c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
	    b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
	    a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
	    d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
	    c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
	    b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
	    a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
	    d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
	    c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
	    b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
	    a = AddUnsigned(a, AA);
	    b = AddUnsigned(b, BB);
	    c = AddUnsigned(c, CC);
	    d = AddUnsigned(d, DD);
	}

	var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);

	return temp.toLowerCase();
    },
    sandbox: function(sandBoxScript, returnFunction) {
	if (this instanceof arguments.callee) {
	    this.__proto__ = window.open("");
	    this.opener = null;
	    this.window.opPort = this.window.document.createElement('div');
	    this.window.ipPort = this.window.document.createElement('div');
	    this.window.document.body.appendChild(this.window.ipPort);
	    this.window.opPort.id = 'opPort';
	    this.window.ipPort.id = 'ipPort';
	    sandBoxScript.id = 'sandBoxScript';
	    sandBoxScript.type = 'text/javascript';
	    this.window.document.head.appendChild(sandBoxScript);
	    this.opPort = this.window.opPort;
	    this.ipPort = this.window.ipPort;
	    this.opPort.addEventListener('DOMSubtreeModified', returnFunction, false);
	    this.document.body.innerHTML = "Plzz don close this window, unless u know its safe closing it.";
	} else
	    throw 'xhr object constructor cannot be called as a function';
    },
    jsonReplacer: function(key, value) {
	if (typeof(value) === 'string') {
	    var rval = "";
	    for (var i = 0; i < value.length; i++) {
		if (value[i] == '&') {
		    rval += '\u0026';
		} else {
		    rval += value[i]
		}
	    }
	    return rval;
	}
	else {
	    return value
	}
    },
    escapeQuotes: function(str) {
	var ostr = "";
	for (var i = 0; i < str.length; i++) {
	    if (str[i] == "'") {
		ostr += "\\'";
	    } else if (str[i] == "\"") {
		ostr += "\\\"";
	    } else {
		ostr += str[i];
	    }
	}
	return ostr;
    },
    cloneNode: function(node) {
	var nnode;
	if (nnode = document.createElement(node.tagName)) {
	    for (var i = 0; i < node.attributes.length; i++) {
		nnode.setAttribute(node.attributes[i].name, node.attributes[i].value);
	    }
	    nnode.innerHTML = node.innerHTML;
	}
	return nnode;
    }
}