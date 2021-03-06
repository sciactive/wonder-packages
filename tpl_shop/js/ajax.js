// Experimental AJAX code.
$_(function(){
var current_hash, changing_hash = false, loader, j_window = $(window),
pos_head = $("head"),
main_menu = $("#main_menu"),
pos_top = $("#top"),
pos_header = $("#header_position"),
pos_header_right = $("#header_right"),
pos_pre_content = $("#pre_content"),
pos_breadcrumbs = $("#breadcrumbs"),
pos_content_top_left = $("#content_top_left"),
pos_content_top_right = $("#content_top_right"),
pos_content = $("#content"),
pos_content_bottom_left = $("#content_bottom_left"),
pos_content_bottom_right = $("#content_bottom_right"),
pos_post_content = $("#post_content"),
pos_left = $("#left"),
pos_right = $("#right"),
pos_footer = $("#footer_position"),
pos_bottom = $("#bottom");
var load_page_ajax = function(url, type, data){
	if (typeof data == "undefined")
		data = {tpl_shop_ajax: 1};
	else if (typeof data == "string") {
		if (data.indexOf("tpl_shop_ajax=1") == -1) {
		if (data != "")
			data += "&"
		data += "tpl_shop_ajax=1";
		}
	} else
		data.tpl_shop_ajax = 1;
	$.ajax({
		"type": type,
		"url": url,
		"dataType": "json",
		"data": data,
		beforeSend: function(xhr) {
			// TODO: Detect redirects.
			if (!loader)
				loader = new PNotify({
					text: "Loading...",
					icon: "picon picon-throbber",
					width: "120px",
					opacity: .6,
					animate_speed: 20,
					nonblock: {
						nonblock: true
					},
					hide: false,
					history: {
						history: false
					},
					stack: {"dir1": "down","dir2": "right"}
				});
			loader.get().css({"top": "-0.6em", "left": (j_window.width() / 2) - (loader.width() / 2)});
			loader.open();
			xhr.setRequestHeader("Accept", "application/json");
		},
		complete: function(){
			loader.remove();
		},
		error: function(xhr, textStatus){
			$_.error("An error occured while communicating with the server:\n\n"+$_.safe(xhr.status)+": "+$_.safe(textStatus));
		},
		success: function(data){
			if (window.location != url) {
				current_hash = "#!"+url.slice($_.rela_location.length);
				changing_hash = true;
				window.location.hash = current_hash;
			} else
				current_hash = "";
			// Pause DOM ready script execution.
			$_.pause();
			pos_head.append(data.pos_head);
			main_menu.html(data.main_menu);
			pos_top.html(data.pos_top);
			pos_header.html(data.pos_header);
			pos_header_right.html(data.pos_header_right);
			pos_pre_content.html(data.pos_pre_content);
			pos_breadcrumbs.html(data.pos_breadcrumbs);
			pos_content_top_left.html(data.pos_content_top_left);
			pos_content_top_right.html(data.pos_content_top_right);
			pos_content.html(data.pos_content);
			pos_content_bottom_left.html(data.pos_content_bottom_left);
			pos_content_bottom_right.html(data.pos_content_bottom_right);
			pos_post_content.html(data.pos_post_content);
			pos_left.html(data.pos_left+"&nbsp;");
			pos_right.html(data.pos_right+"&nbsp;");
			pos_footer.html(data.pos_footer);
			pos_bottom.html(data.pos_bottom);
			$.each(data.errors, function(){
				$_.error($_.safe(this), "Error");
			});
			$.each(data.notices, function(){
				$_.notice($_.safe(this), "Notice");
			});
			// Now run DOM ready scripts.
			$_.play();
		}
	});
};
$("body").on("click", "a", function(e){
	var cur_elem = $(this);
	var target = cur_elem.attr("target");
	if (typeof target != "undefined" && target != "" && target != "_self")
		return true;
	var url = cur_elem.attr("href");
	if (typeof url == "undefined")
		return true;
	if (url == "#")
		return false;
	if (url.indexOf($_.rela_location) != 0)
		return true;
	e.preventDefault();
	load_page_ajax(url, "GET");
	return false;
}).on("submit", "form", function(){
	// TODO: Check for file elements.
	var cur_elem = $(this);
	var url = cur_elem.attr("action");
	if (url.indexOf($_.rela_location) != 0)
		return true;
	var data = cur_elem.serialize();
	load_page_ajax(url, "POST", data);
	return false;
});
$_.get = function(url, params, target){
	if (!target || target == "_self") {
		if (params)
			params.tpl_shop_ajax = 1;
		else
			params = {tpl_shop_ajax: 1};
	}
	if (params) {
		url += (url.indexOf("?") == -1) ? "?" : "&";
		var parray = [];
		for (var i in params) {
			if (params.hasOwnProperty(i)) {
				if (encodeURIComponent)
					parray.push(encodeURIComponent(i)+"="+encodeURIComponent(params[i]));
				else
					parray.push(escape(i)+"="+escape(params[i]));
			}
		}
		url += parray.join("&");
	}
	if (!target || target == "_self") {
		if (url.indexOf($_.rela_location) != 0) {
			window.location = url;
			return;
		}
		load_page_ajax(url, "GET");
	} else if (target == "_top")
		window.top.location = url;
	else if (target == "_parent")
		window.parent.location = url;
	else if (target == "_blank")
		window.open(url);
	else
		window.open(url, target);
};
// TODO: Handle $_.post through Ajax.

// Load any page found on the hash.
if (typeof window.location.hash == "string" && window.location.hash != "" && window.location.hash.indexOf("!") == 1)
	load_page_ajax($_.rela_location + window.location.hash.slice(2), "GET");

// When the hash changes (like the back button) load the new page.
var hashchange = function(){
	// Check that the hash hasn't been loaded.
	if (window.location.hash == current_hash || changing_hash) {
		// This accounts for URL encoding being changed in the hash.
		current_hash = window.location.hash;
		changing_hash = false;
		return;
	}
	// Load the new hash.
	if (typeof window.location.hash == "string" && window.location.hash != "") {
		if (window.location.hash.indexOf("!") == 1)
			load_page_ajax($_.rela_location + window.location.hash.slice(2), "GET");
	} else
		load_page_ajax(window.location, "GET");
};
if ("onhashchange" in window)
	window.onhashchange = hashchange;
else
	window.setInterval(hashchange, 100);
});