/*jslint sloppy: true, maxerr: 50, indent: 4 */
/*global window, document, jQuery, $ */

var allowServerUpdate = true, // in some cases, mostly during initialization, we dont want to update the server when the form onchange event occurs
	pubKey,
	themeTracker, // keep track of our themes; whether they be custom or presets etc..
	globalSettingsObject,
	themeBuilder = {
		init: function (k, o) {
			pubKey = k;
			globalSettingsObject = $.extend(true, {}, o); // javascript passes objects by reference, make a clone of the object			
			
			
			
			fn.ajax("getRelevantThemes", {
				pk: pubKey
			}, function (data) {
				
				var settingsForm = $("#settings"),
					emmaThemePreset = $("#emmaThemePreset"),
					emmaThemeCustom = $("#emmaThemeCustom"),
					emmaThemePresetButtons;
				

				
	

				// we can't hide the pages with css so remove the work-around
				$(".themer-pages").css({
					visibility: "visible",
					height: "auto"
				});

				// insert the options for the "Dot Theme" dropdown..
				$("#frontendResourceFiles", settingsForm).html(function () {
					var strHtml = "";
					$.each(data.resourceFiles, function (key, val) {
						strHtml += '<li class="dropdown-option"><span class="dropdown-thumbnail"><img class="thumbnail" src="' + val + '" /></span></li>';
					});
					return strHtml;
				});

				// insert the theme preset thumbnail buttons
				$("#themePresetButtons").html(function () {
					var strHtml = "";
					$.each(data.resourceFiles, function (key, val) {
						strHtml += '<button class="btn-load-theme" type="button" name="' + key + '"><span class="checkbox"></span><img src="plugins/thememanager/thumbs/' + key + '.jpg" /></button>';
					});
					return strHtml;
				});

				// wait until after we add the buttons before looking
				emmaThemePresetButtons = emmaThemePreset.find(".btn-load-theme");
				
				
				
				// MANAGE SPECIAL FORM EVENTS
				$("[toggleContainer]", settingsForm).change(toggleContainer).each(toggleContainer);
				$("#backgroundStyle", settingsForm).change(changeBackgroundStyle).each(changeBackgroundStyle);
				$(".number", settingsForm).numberSpinner();
				$(".divbox-dropdown", settingsForm).boxDropDown();
				$(".iphoneStyle", settingsForm).iphoneStyle({
					onChange: function (element, value) {
						element.trigger("change");
					}
				});


				// MANAGE CUSTOM/PRESET PAGE EVENTS
				emmaThemePresetButtons.click(function () {
					var themeName = $(this).attr("name");
					if (themeTracker !== themeName && (themeTracker !== "custom" || confirm("Are you sure you want to load a preset theme? This will erase any custom settings you may have made."))) {
						$(this).addClass("active").siblings().removeClass("active");
						globalSettingsObject = $.extend(true, {}, data.themeJS[themeName]); // javascript passes objects by reference, make a clone of the object
						updateFormValues.call(settingsForm, globalSettingsObject, data.themeCSS[themeName]);
						themeTracker = themeName;
						settingsForm.each(updateServerValues);
					}
				});
				$("#selectPresetTheme").click(function () {
					$(this).addClass("active").siblings().removeClass("active");
					if (emmaThemePreset.prop("hasFocus") === true) { return false; }
					emmaThemeCustom.removeProp("hasFocus");
					emmaThemePreset.prop("hasFocus", true);
					emmaThemeCustom.hide();
					emmaThemePreset.show();
					return false;
				});
				$("#buildCustomTheme").click(function () {
					$(this).addClass("active").siblings().removeClass("active");
					if (emmaThemeCustom.prop("hasFocus") === true) { return false; }
					emmaThemePreset.removeProp("hasFocus");
					emmaThemeCustom.prop("hasFocus", true);
					emmaThemePreset.hide();
					emmaThemeCustom.show();
					fn.ajax("getThemeCss", {
						themeFile: globalSettingsObject.themeFile
					}, function (data) {
						updateFormValues.call(settingsForm, globalSettingsObject, data.cssObject);
					});
					return false;
				});


				
				themeTracker = data.themeTracker;


				if (themeTracker === "custom") {
					$("#buildCustomTheme").trigger("click");
				} else {
					emmaThemePresetButtons.filter("[name='" + themeTracker + "']").addClass("active");
					$("#selectPresetTheme").trigger("click");
				}

				// this should be the last thing we do so that all the above stuff doesn't set it off
				settingsForm.change(function () {
					if (allowServerUpdate) {
						themeTracker = "custom";
						emmaThemePresetButtons.removeClass("active");
						updateServerValues.call(this);
					}
				});
				
			
			});
			
			
		}
	},
	lib = {
		isNumeric: function (n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		}
	},
	fn = {

		gradients: {

			/*
			convert two colors into one string which represents a gradient
			ex: #FFF and #000 becomes "0-#FFF 1-#000"
			*/
			merge: function (colorInfo) {
				if (this.canvas && this.canvas.theme && this.canvas.theme[colorInfo[0]] && this.canvas.theme[colorInfo[1]]) {
					this.canvas.theme.backgroundColor = "0-" + this.canvas.theme[colorInfo[0]] + " 1-" + this.canvas.theme[colorInfo[1]];
					delete this.canvas.theme[colorInfo[0]];
					delete this.canvas.theme[colorInfo[1]];
				}
				return this;
			},

			/*
			extract hex colors from a gradient string
			ex: "0-#FFF 1-#000" becomes #FFF and #000
			*/
			expand: function (method, properties) {
				var colorInfo,
					i;

				if (this.canvas && this.canvas.theme && this.canvas.theme.backgroundColor) {
					if (this.canvas.theme.backgroundStyle === method) {
						colorInfo = this.canvas.theme.backgroundColor.split(" ");
						for (i = 0; i < colorInfo.length; i += 1) {
							this.canvas.theme[properties[i]] = colorInfo[i].split("-")[1];
						}
						delete this.canvas.theme.backgroundColor;
					}
				}
				return this;
			}

		},

		ajax: function (file, params, method) {
			$("#ajaxNotifier").addClass("active");
			$.post("plugins/thememanager/php/ajax/" + file + ".ajax.php", params, function (data) {
				if (data) {
					if (data.error) { 
						alert(data.error); 
					} else if (method && data.result) {
						method(data.result); 
					} 
				} else if (method) {
					method(); 
				}
				$("#ajaxNotifier").removeClass("active");
			});
		}

	};





(function ($) {

	$.fn.disableFormElements = function () {
		return this.each(function () {
			$(this).prop("disabled", "disabled");
		});
	};

	$.fn.enableFormElements = function () {
		return this.each(function () {
			$(this).removeProp("disabled");
		});
	};


	/*
	@description: This plugin creates a number spinner for your forms
	@author: Justin Bull
	@note: We combine rapid input changes into one event so not to overload it
	It expects the following HTML layout to work properly:
	<div class="number">
		<input type="text" class="spinInput" value="0" />
		<button type="button" class="spinUp"></button>
		<button type="button" class="spinDown"></button>
	</div>
	*/
	$.fn.numberSpinner = function () {
		return this.each(function () {
			var elmInput = $(this).find(".spinInput"),
				minVal = elmInput.attr("minValue") || 0,
				maxVal = elmInput.attr("maxValue") || 100,
				btnSpinUp = $(this).find(".spinUp"),
				btnSpinDown = $(this).find(".spinDown"),
				update = function (val) {
					elmInput.val(val);
					window.clearTimeout(elmInput.timeoutID);
					elmInput.timeoutID = window.setTimeout(function () {
						elmInput.trigger("change");
					}, 500);
				};
			btnSpinUp.click(function () {
				var currVal = parseInt(elmInput.val(), 10),
					nextVal = (currVal < maxVal) ? currVal += 1 : maxVal;
				update(nextVal);
				return false;
			});
			btnSpinDown.click(function () {
				var currVal = parseInt(elmInput.val(), 10),
					prevVal = (currVal > minVal) ? currVal -= 1 : minVal;
				update(prevVal);
				return false;
			});
			elmInput.keyup(function () {
				var currVal = parseInt(elmInput.val(), 10),
					newVal;
				if (isNaN(currVal)) {
					newVal = 0;
				} else if (currVal > maxVal) {
					newVal = maxVal;
				} else if (currVal < minVal) {
					newVal = minVal;
				} else {
					newVal = currVal;
				}
				update(newVal);
			});
		});
	};



	$.fn.boxDropDown = function () {
		return this.each(function () {
			var dropdown = $(this),
				dropdownWrapper = dropdown.find(".dropdownWrapper");
			dropdownWrapper.find(".selected-thumbnail").click(function () {
				dropdownWrapper.toggleClass("active");
			});
			dropdownWrapper.find(".dropdown-option").click(function () {
				var selectedSource = $(this).find(".thumbnail").attr("src");
				$(this).addClass("selected").siblings().removeClass("selected");
				dropdownWrapper.removeClass("active").find(".selected-thumbnail > .thumbnail").attr("src", selectedSource);
				dropdown.find(".dropdown-value").val(selectedSource).trigger("change");
			}).eq(0).trigger("click");
		});
	};


}(jQuery));






function toggleContainer() {
	var containerID = "#" + $(this).attr("toggleContainer");
	if ($(this).is(":checked")) {
		$(containerID).show().find(":input").enableFormElements();
	} else {
		$(containerID).hide().find(":input").disableFormElements();
	}
}


function changeBackgroundStyle() {
	$(".backgroundStyle-option").hide().find(":input").disableFormElements();
	$("#backgroundStyle-" + $(this).val() + "-option").show().find(":input").enableFormElements();
}




function updateFormValues(jsObject, cssObject) {

	var settingsForm = this,
		settings = $.extend(true, {}, jsObject), // javascript passes objects by reference, make a clone of the object
		arrBoolChanged = [],
		recursiveUpdate = function (object, scope) {

			$.each(object, function (index, value) {

				var formElement,
					checked,
					s = (scope) ? scope + "." + index : index;

				if ($.type(value) === "object") {
					recursiveUpdate(value, s);
				} else {
					formElement = $("[name='" + s + "']", settingsForm);
					if (formElement.length > 0) {

						if ($.type(value) === "boolean") {
							checked = formElement.is(":checked");
							if (value === true && !checked) {
								formElement.prop("checked", true);
								arrBoolChanged.push(formElement);
							} else if (value === false && checked) {
								formElement.prop("checked", false);
								arrBoolChanged.push(formElement);
							}

						} else {

							formElement.val(value);
						}

					}
				}
			});

		};


	// expand gradient string into its hex color counterparts
	settings = fn.gradients.expand.call(settings, "linear", ["linearGradient1", "linearGradient2"]);
	settings = fn.gradients.expand.call(settings, "radial", ["radialGradient1", "radialGradient2"]);


	recursiveUpdate(settings);

	// update the iphone toggle switches without triggering a server update; caused by the form's onchange event
	$.each(arrBoolChanged, function (index, value) {
		allowServerUpdate = false;
		value.iphoneStyle("refresh");
		allowServerUpdate = true;
	});

	// update special elements
	$("[toggleContainer]", settingsForm).each(toggleContainer);
	$("#backgroundStyle", settingsForm).each(changeBackgroundStyle); // check current background style


	$.each(cssObject, function (key, val) {
		// assume non-multidimensional and input:text
		if (val.charAt(0) === "#") {
			// jscolor plugin update method
			settingsForm[0].elements[key].color.fromString(val);
		} else if (val.slice(0, 4) === "http") {
			// assume theme resource update
			allowServerUpdate = false;
			$("[name='" + key + "']", settingsForm).siblings(".dropdownWrapper").find(".dropdown-options").find("[src='" + val + "']").parent().trigger("click");
			allowServerUpdate = true;
		} else {
			$("[name='" + key + "']", settingsForm).val(val);
		}
	});


}



function updateServerValues() {

	var settingsForm = $(this),
		
		formElements = {
			jsMethod: settingsForm.find("[method='js']").find(":input:not(:disabled)"),
			cssMethod: settingsForm.find("[method='css']").find(":input:not(:disabled)")
		},

		settings = {},
		css = {},

		recursiveUpdate = function (properties, value) {
			var property = properties.shift();
			if (properties.length > 0) {
				this[property] = (this.hasOwnProperty(property)) ? this[property] : {};
				recursiveUpdate.call(this[property], properties, value);
			} else {
				this[property] = value;
			}
		};


	//if (!allowServerUpdate) { return; } // self explanatory

	$.each(formElements.jsMethod, function () {

		var element = $(this),
			inputType = element.attr("type"),
			inputName = element.attr("name"),
			inputValue = element.val(),
			setValue;


		if (!inputName) { return; } // filter out buttons


		if (inputType === "checkbox") {
			setValue = element.is(":checked");
		} else if (lib.isNumeric(inputValue)) {
			setValue = parseFloat(inputValue);
		} else {
			setValue = inputValue;
		}


		/*
		This will use the form element name to determine the setting scope
		@EXAMPLE:
		<input name="canvas.theme.backgroundColor" value="red" />
		@RESULT:
		var settings = {
				canvas: {
					theme: {
						backgroundColor: red
					}
				}
			};
		*/
		recursiveUpdate.call(settings, inputName.split("."), setValue);



	});


	// merge any gradient colors into one string
	settings = fn.gradients.merge.call(settings, ["linearGradient1", "linearGradient2"]);
	settings = fn.gradients.merge.call(settings, ["radialGradient1", "radialGradient2"]);

	// send our js settings to the server where we save it to our database
	fn.ajax("setSiteSettings", {
		pk: pubKey,
		settings: settings,
		themeTracker: themeTracker
	});


	// loop over form elements that have the css method attribute and extract their name and value
	$.each(formElements.cssMethod, function () {
		var element = $(this),
			inputName = element.attr("name"),
			inputValue = element.val();
		if (!inputName) { return; } // filter out buttons
		css[inputName] = inputValue;
	});

	// get the css file assigned to this site guid
	fn.ajax("getCssFile", {
		pk: pubKey
	}, function (data) {

		// if a preset theme is selected, the default JS object that is provided does not contain the themeFile property/value
		// since it is needed when building a custom theme, let us go ahead and add it here
		globalSettingsObject.themeFile = data.filename;

		// use the file name as the wrapper element's z-index for potential file verification
		var zIndex = data.filename.split(".")[0];
		if (lib.isNumeric(zIndex)) {
			css.wrapperIndex = zIndex;
			$("#EMMA_active").addClass("EMMA_" + zIndex);
			// send the css to the server for parsing and then save it to file
			fn.ajax("setThemeCss", css);
		}
	});




}



