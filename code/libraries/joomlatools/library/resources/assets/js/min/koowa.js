var globalCacheForjQueryReplacement=window.jQuery;if(window.jQuery=window.kQuery,function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t(kQuery)}(function(t){var e=0,o=Array.prototype.slice;t.cleanData=function(e){return function(o){var i,n,a;for(a=0;null!=(n=o[a]);a++)try{i=t._data(n,"events"),i&&i.remove&&t(n).triggerHandler("remove")}catch(s){}e(o)}}(t.cleanData),t.widget=function(e,o,i){var n,a,s,r,c={},l=e.split(".")[0];return e=e.split(".")[1],n=l+"-"+e,i||(i=o,o=t.Widget),t.expr[":"][n.toLowerCase()]=function(e){return!!t.data(e,n)},t[l]=t[l]||{},a=t[l][e],s=t[l][e]=function(t,e){return this._createWidget?void(arguments.length&&this._createWidget(t,e)):new s(t,e)},t.extend(s,a,{version:i.version,_proto:t.extend({},i),_childConstructors:[]}),r=new o,r.options=t.widget.extend({},r.options),t.each(i,function(e,i){return t.isFunction(i)?void(c[e]=function(){var t=function(){return o.prototype[e].apply(this,arguments)},n=function(t){return o.prototype[e].apply(this,t)};return function(){var e,o=this._super,a=this._superApply;return this._super=t,this._superApply=n,e=i.apply(this,arguments),this._super=o,this._superApply=a,e}}()):void(c[e]=i)}),s.prototype=t.widget.extend(r,{widgetEventPrefix:a?r.widgetEventPrefix||e:e},c,{constructor:s,namespace:l,widgetName:e,widgetFullName:n}),a?(t.each(a._childConstructors,function(e,o){var i=o.prototype;t.widget(i.namespace+"."+i.widgetName,s,o._proto)}),delete a._childConstructors):o._childConstructors.push(s),t.widget.bridge(e,s),s},t.widget.extend=function(e){for(var i,n,a=o.call(arguments,1),s=0,r=a.length;r>s;s++)for(i in a[s])n=a[s][i],a[s].hasOwnProperty(i)&&void 0!==n&&(t.isPlainObject(n)?e[i]=t.isPlainObject(e[i])?t.widget.extend({},e[i],n):t.widget.extend({},n):e[i]=n);return e},t.widget.bridge=function(e,i){var n=i.prototype.widgetFullName||e;t.fn[e]=function(a){var s="string"==typeof a,r=o.call(arguments,1),c=this;return s?this.each(function(){var o,i=t.data(this,n);return"instance"===a?(c=i,!1):i?t.isFunction(i[a])&&"_"!==a.charAt(0)?(o=i[a].apply(i,r),o!==i&&void 0!==o?(c=o&&o.jquery?c.pushStack(o.get()):o,!1):void 0):t.error("no such method '"+a+"' for "+e+" widget instance"):t.error("cannot call methods on "+e+" prior to initialization; attempted to call method '"+a+"'")}):(r.length&&(a=t.widget.extend.apply(null,[a].concat(r))),this.each(function(){var e=t.data(this,n);e?(e.option(a||{}),e._init&&e._init()):t.data(this,n,new i(a,this))})),c}},t.Widget=function(){},t.Widget._childConstructors=[],t.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(o,i){i=t(i||this.defaultElement||this)[0],this.element=t(i),this.uuid=e++,this.eventNamespace="."+this.widgetName+this.uuid,this.bindings=t(),this.hoverable=t(),this.focusable=t(),i!==this&&(t.data(i,this.widgetFullName,this),this._on(!0,this.element,{remove:function(t){t.target===i&&this.destroy()}}),this.document=t(i.style?i.ownerDocument:i.document||i),this.window=t(this.document[0].defaultView||this.document[0].parentWindow)),this.options=t.widget.extend({},this.options,this._getCreateOptions(),o),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:t.noop,_getCreateEventData:t.noop,_create:t.noop,_init:t.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetFullName).removeData(t.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:t.noop,widget:function(){return this.element},option:function(e,o){var i,n,a,s=e;if(0===arguments.length)return t.widget.extend({},this.options);if("string"==typeof e)if(s={},i=e.split("."),e=i.shift(),i.length){for(n=s[e]=t.widget.extend({},this.options[e]),a=0;a<i.length-1;a++)n[i[a]]=n[i[a]]||{},n=n[i[a]];if(e=i.pop(),1===arguments.length)return void 0===n[e]?null:n[e];n[e]=o}else{if(1===arguments.length)return void 0===this.options[e]?null:this.options[e];s[e]=o}return this._setOptions(s),this},_setOptions:function(t){var e;for(e in t)this._setOption(e,t[e]);return this},_setOption:function(t,e){return this.options[t]=e,"disabled"===t&&(this.widget().toggleClass(this.widgetFullName+"-disabled",!!e),e&&(this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus"))),this},enable:function(){return this._setOptions({disabled:!1})},disable:function(){return this._setOptions({disabled:!0})},_on:function(e,o,i){var n,a=this;"boolean"!=typeof e&&(i=o,o=e,e=!1),i?(o=n=t(o),this.bindings=this.bindings.add(o)):(i=o,o=this.element,n=this.widget()),t.each(i,function(i,s){function r(){return e||a.options.disabled!==!0&&!t(this).hasClass("ui-state-disabled")?("string"==typeof s?a[s]:s).apply(a,arguments):void 0}"string"!=typeof s&&(r.guid=s.guid=s.guid||r.guid||t.guid++);var c=i.match(/^([\w:-]*)\s*(.*)$/),l=c[1]+a.eventNamespace,d=c[2];d?n.delegate(d,l,r):o.bind(l,r)})},_off:function(e,o){o=(o||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,e.unbind(o).undelegate(o),this.bindings=t(this.bindings.not(e).get()),this.focusable=t(this.focusable.not(e).get()),this.hoverable=t(this.hoverable.not(e).get())},_delay:function(t,e){function o(){return("string"==typeof t?i[t]:t).apply(i,arguments)}var i=this;return setTimeout(o,e||0)},_hoverable:function(e){this.hoverable=this.hoverable.add(e),this._on(e,{mouseenter:function(e){t(e.currentTarget).addClass("ui-state-hover")},mouseleave:function(e){t(e.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(e){this.focusable=this.focusable.add(e),this._on(e,{focusin:function(e){t(e.currentTarget).addClass("ui-state-focus")},focusout:function(e){t(e.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(e,o,i){var n,a,s=this.options[e];if(i=i||{},o=t.Event(o),o.type=(e===this.widgetEventPrefix?e:this.widgetEventPrefix+e).toLowerCase(),o.target=this.element[0],a=o.originalEvent)for(n in a)n in o||(o[n]=a[n]);return this.element.trigger(o,i),!(t.isFunction(s)&&s.apply(this.element[0],[o].concat(i))===!1||o.isDefaultPrevented())}},t.each({show:"fadeIn",hide:"fadeOut"},function(e,o){t.Widget.prototype["_"+e]=function(i,n,a){"string"==typeof n&&(n={effect:n});var s,r=n?n===!0||"number"==typeof n?o:n.effect||o:e;n=n||{},"number"==typeof n&&(n={duration:n}),s=!t.isEmptyObject(n),n.complete=a,n.delay&&i.delay(n.delay),s&&t.effects&&t.effects.effect[r]?i[e](n):r!==e&&i[r]?i[r](n.duration,n.easing,a):i.queue(function(o){t(this)[e](),a&&a.call(i[0]),o()})}});t.widget}),function(t,e,o){o.widget("koowa.scopebar",{widgetEventPrefix:"scopebar:",options:{template:function(){}},_create:function(){var t=o(".js-filter-prototype");this.template=t.clone(),this.template.removeClass(".js-filter-prototype"),t.remove(),this._addEvents();var e=o(".js-filter-container");o(".js-filters div[data-filter]").each(function(i,n){var a=t.clone();n=o(this),n.addClass("js-dropdown-content k-dropdown__body__content"),a.find(".js-dropdown-body").prepend(n),a.find(".js-dropdown-title").html(n.data("title"));var s=a.find(".js-dropdown-label"),r=n.data("label"),c=n.data("count");c&&c>0&&(r=c),r?s.html(r):s.hide(),n.show(),a.show(),e.append(a),o(".js-filter-count").text(e.find(".js-dropdown-label:visible").length)})},_addEvents:function(){var t=this,i=function(){return o(".js-dropdown").hasClass("is-active")};o(e).keyup(function(e){if(39==e.keyCode&&i()){var n=o(".js-dropdown.is-active").next().find(o(".js-dropdown-button"));n.hasClass("js-dropdown-button")&&(t.closeDropdown(),t.openDropdown(n))}if(37==e.keyCode&&i()){var a=o(".js-dropdown.is-active").prev().find(o(".js-dropdown-button"));a.hasClass("js-dropdown-button")&&(t.closeDropdown(),t.openDropdown(a))}27==e.keyCode&&i()&&t.closeDropdown()}),o("html").click(function(e){var i=o(e.target),n=-1!==e.target.className.search("select2-"),a=-1!==i.parents(".datepicker-dropdown").length;n||a||0!==i.parents(".js-filter-container").length||t.closeDropdown()}),this.element.on("click","*",function(e){var i=o(e.target);i.hasClass("js-dropdown-button")||(i=i.parents(".js-dropdown-button")),0!==i.length&&(i.parent().hasClass("is-active")?t.closeDropdown():t.openDropdown(i),e.stopPropagation())}),this.element.on("mouseenter mouseleave","*",function(e){var n=o(e.target);n.hasClass("js-dropdown-button")||(n=n.parents(".js-dropdown-button")),0!==n.length&&i()&&!n.parent().hasClass("is-active")&&(t.closeDropdown(),t.openDropdown(n),n.focus())}),submitForm=function(t,e){e.find("select").each(function(e,i){var n=o(i).val();if(!n||""===n||"object"==typeof n&&1===n.length&&""===n[0]){var a=o(i).attr("name");a=a.replace("[]",""),o(i).removeAttr("name"),o(t).append('<input type="hidden" name="'+a+'" value="" />')}}),t.submit()},this.element.on("click",".js-clear-filter",function(t){t.preventDefault();var e=o(t.target).parents(".js-dropdown");e.find(":input").not(":button, :submit, :reset, :hidden").removeAttr("checked").removeAttr("selected").not(":checkbox, :radio").val("").filter("select").trigger("change");var i=t.target.form;i&&submitForm(i,e)}).on("click",".js-apply-filter",function(t){t.preventDefault();var e=t.target.form,i=o(t.target).parents(".js-dropdown");e&&submitForm(e,i)})},openDropdown:function(t){var e=t.parent();this.closeDropdown(),e.addClass("is-active");var o=e.find("select");1===o.length&&o.data("select2"),t.focus()},closeDropdown:function(){var t=o(".js-dropdown.is-active"),e=t.find("select");t.removeClass("is-active"),e.data("select2")&&e.select2("close")}})}(window,document,kQuery),!Koowa)var Koowa={};Function.prototype.bind||(Function.prototype.bind=function(t){if("function"!=typeof this)throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");var e=Array.prototype.slice.call(arguments,1),o=this,i=function(){},n=function(){return o.apply(this instanceof i&&t?this:t,e.concat(Array.prototype.slice.call(arguments)))};return i.prototype=this.prototype,n.prototype=new i,n});var klass=function(){function t(t){return n.call(e(t)?t:function(){},t,1)}function e(t){return typeof t===r}function o(t,e,o){return function(){var i=this.supr;this.supr=o[l][t];var n={}.fabricatedUndefined,a=n;try{a=e.apply(this,arguments)}finally{this.supr=i}return a}}function i(t,i,n){for(var a in i)i.hasOwnProperty(a)&&(t[a]=e(i[a])&&e(n[l][a])&&c.test(i[a])?o(a,i[a],n):i[a])}function n(t,o){function n(){}function a(){this.initialize?this.initialize.apply(this,arguments):(o||c&&s.apply(this,arguments),d.apply(this,arguments))}n[l]=this[l];var s=this,r=new n,c=e(t),d=c?t:this,h=c?{}:t;return a.methods=function(t){return i(r,t,s),a[l]=r,this},a.methods.call(a,h).prototype.constructor=a,a.extend=arguments.callee,a[l].implement=a.statics=function(t,e){return t="string"==typeof t?function(){var o={};return o[t]=e,o}():t,i(this,t,s),this},a}var a=this,s=a.klass,r="function",c=/xyz/.test(function(){xyz})?/\bsupr\b/:/.*/,l="prototype";return t.noConflict=function(){return a.klass=s,this},t}();if(function(t){Koowa.Class=klass({options:{},getOptions:function(){return{}},initialize:function(){this.setOptions(this.getOptions())},setOptions:function(e){return"object"==typeof e&&(this.options=t.extend(!0,{},this.options,e)),this}})}(window.kQuery),"undefined"==typeof Koowa&&(Koowa={}),function(t){Koowa.Grid=Koowa.Class.extend({initialize:function(e){var o=this;this.element=t(e),this.form=this.element.is("form")?this.element:this.element.closest("form"),this.toggles=this.element.find(".-koowa-grid-checkall"),this.checkboxes=this.element.find(".-koowa-grid-checkbox").filter(function(e,o){return!t(o).prop("disabled")}),this.checkboxes.length||this.toggles.prop("disabled",!0),this.toggles.on("change.koowa",function(e,i){i||o.checkAll(t(this).prop("checked"))}),this.checkboxes.on("change.koowa",function(t,e){e||o.setCheckAll()})},checkAll:function(e){var o=this.checkboxes.filter(function(o,i){return t(i).prop("checked")!==e});this.checkboxes.prop("checked",e),o.trigger("change",!0)},uncheckAll:function(){this.checkAll(!1)},setCheckAll:function(){var e=this.checkboxes.filter(function(e,o){return t(o).prop("checked")!==!1}).length;this.toggles.prop("checked",this.checkboxes.length===e),this.toggles.trigger("change",!0)}}),Koowa.Grid.getAllSelected=function(e){return t(".-koowa-grid-checkbox:checked",e)},Koowa.Grid.getIdQuery=function(t){return decodeURIComponent(this.getAllSelected(t).serialize())}}(window.kQuery),!Koowa)var Koowa={};!function(t){t(function(){t(".submittable").on("click.koowa",function(e){e.preventDefault(),new Koowa.Form(t(e.target).data("config")).submit()}),t(".-koowa-grid").each(function(){new Koowa.Controller.Grid({form:this})}),t(".-koowa-form").each(function(){new Koowa.Controller.Form({form:this})})}),Koowa.Translator||(Koowa.Translator=Koowa.Class.extend({translations:{},translate:function(t,e){if("undefined"!=typeof this.translations[t.toLowerCase()]&&(t=this.translations[t.toLowerCase()]),"object"==typeof e&&null!==e)for(var o in e)if(e.hasOwnProperty(o)){var i="{"+o+"}".replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&");t=t.replace(new RegExp(i,"g"),e[o])}return t},loadTranslations:function(t){for(var e in t)t.hasOwnProperty(e)&&(this.translations[e.toLowerCase()]=t[e]);return this}}),Koowa.translator=new Koowa.Translator,Koowa.translate=Koowa.translator.translate.bind(Koowa.translator)),Koowa.Form=Koowa.Class.extend({initialize:function(e){this.config=e,this.config.element?this.form=t(document[this.config.element]):(this.form=t("<form/>",{name:"dynamicform",method:this.config.method||"POST",action:this.config.url}),t(document.body).append(this.form))},addField:function(e,o){var i=t("<input/>",{name:e,value:o,type:"hidden"});return i.appendTo(this.form),this},submit:function(){var e=this;this.config.params&&t.each(this.config.params,function(t,o){e.addField(t,o)}),this.form.submit()}}),Koowa.Controller=Koowa.Class.extend({form:null,toolbar:null,buttons:null,token_name:null,token_value:null,getOptions:function(){return t.extend(this.supr(),{toolbar:".k-toolbar",url:window.location.href})},initialize:function(e){var o=this;this.supr(),this.setOptions(e),this.form=t(this.options.form),this.setOptions(this.form.data()),this.form.prop("action")&&(this.options.url=this.form.attr("action")),this.toolbar=t(this.options.toolbar),this.form.data("controller",this),this.on("execute",function(){return o.execute.apply(o,arguments)}),this.token_name=this.form.data("token-name"),this.token_value=this.form.data("token-value"),this.toolbar&&this.setToolbar()},setToolbar:function(){var e=this;this.buttons=this.toolbar.find(".toolbar[data-action]"),this.buttons.each(function(){var o=t(this),i={},n=o.data(),a=n.data;n.eventAdded||("object"!=typeof a&&(a=a&&"string"===t.type(a)?t.parseJSON(a):{}),e.token_name&&(a[e.token_name]=e.token_value),i.validate="novalidate"!==n.novalidate,i.data=a,i.action=n.action,o.on("click.koowa",function(t){if(t.preventDefault(),i.trigger=o,!o.hasClass("disabled")){var a=o.data("prompt");if(a&&!confirm(a))return;e.setOptions(n),e.trigger("execute",[i])}}),o.data("event-added",!0))})},execute:function(t,e){if(e.action[0]){var o=e.action[0].toUpperCase()+e.action.substr(1),i="_action"+o;"undefined"==typeof e.validate&&(e.validate=!0),this.trigger("before"+o,e)&&(i=this[i]?i:"_actionDefault",this[i].call(this,e),this.trigger("after"+o,e))}return this},on:function(t,e){return this.form.on("koowa:"+t,e)},off:function(t,e){return this.form.off("koowa:"+t,e)},trigger:function(e,o){var i=t.Event("koowa:"+e);return this.form.trigger(i,o),!i.isDefaultPrevented()},checkValidity:function(){var t;this.buttons&&(this.trigger("beforeValidate"),t=this.buttons.filter('[data-novalidate!="novalidate"]'),this.trigger("validate")?t.removeClass("disabled"):t.addClass("disabled"),this.trigger("afterValidate"))}}),Koowa.Controller.Grid=Koowa.Controller.extend({getOptions:function(){return t.extend(this.supr(),{inputs:".-koowa-grid-checkbox, .-koowa-grid-checkall",ajaxify:!1})},initialize:function(t){var e=this;this.supr(t),this.grid=new Koowa.Grid(this.form),this.on("validate",this.validate),this.options.inputs&&this.buttons&&(this.checkValidity(),this.form.find(this.options.inputs).on("change.koowa",function(t,o){o||e.checkValidity()})),this.token_name=this.form.data("token-name"),this.token_value=this.form.data("token-value"),this.setTableHeaders(),this.setTableRows(),this.setFilters(),this.form.find("thead select, tfoot select, .k-pagination select").on("change.koowa",function(){e.grid.uncheckAll(),e.options.ajaxify&&(event.preventDefault(),e.options.transport(e.options.url,e.form.serialize(),"get")),e.form.submit()})},setFilters:function(){t(".js-filter-container").scopebar()},setTableHeaders:function(){this.form.find("thead tr > *").each(function(){var e=t(this),o=e.find("a"),i=e.find(".-koowa-grid-checkall");return o.length?(e.on("click.koowa",function(t){t.target==e[0]&&(o.prop("href")?window.location.href=o.prop("href"):o.trigger("click",t))}),o.hasClass("-koowa-asc")?e.addClass("-koowa-asc"):o.hasClass("-koowa-desc")&&e.addClass("-koowa-desc"),this):(i.length&&e.on("click.koowa",function(t){return t.target!=e[0]?!0:void i.prop("checked",!i.is(":checked")).trigger("change")}),void e.addClass("void"))})},setTableRows:function(){var e=this,o=this.form.find("tbody tr .-koowa-grid-checkbox");this.form.find("tbody tr").each(function(){var i=t(this),n=i.find(".-koowa-grid-checkbox");1!=i.data("readonly")&&n.length&&(i.on("click.koowa",function(e){var o=t(e.target);o.is("[type=radio], [type=checkbox], a[href], span.footable-toggle")||n.prop("checked",!n.prop("checked")).trigger("change")}),n.on("change.koowa",function(){var e,o=i.parent();t(this).is("[type=radio]")&&o.find(".selected").removeClass("selected"),t(this).prop("checked")?i.addClass("selected"):i.removeClass("selected"),e=i.hasClass("selected")+i.siblings(".selected").length,e>1?o.addClass("selected-multiple").removeClass("selected-single"):o.removeClass("selected-multiple").addClass("selected-single")}).trigger("change",!0),i.find("[data-action]").each(function(){var i=t(this),a={},s=i.data("data"),r=i.data(),c=i.data("event-type");"object"!=typeof s&&(s=s&&"string"===t.type(s)?t.parseJSON(s):{}),e.token_name&&(s[e.token_name]=e.token_value),c||(c=i.is('[type="radio"],[type="checkbox"],select')?"change":"click"),a.validate="novalidate"!==r.novalidate,a.data=s,a.action=r.action,i.on(c+".koowa",function(){o.prop("checked",""),n.prop("checked","checked"),o.trigger("change",!0),a.trigger=i,e.setOptions(r),e.trigger("execute",[a])})}))})},validate:function(){return Koowa.Grid.getIdQuery()||!1},_actionDelete:function(t){return t.method="delete",this._actionDefault(t)},_actionDefault:function(e){var o,i=Koowa.Grid.getIdQuery(),n=this.options.url.match(/\?/)?"&":"?";return e.validate&&!this.trigger("validate",[e])?!1:(o={method:"post",url:this.options.url+(i?n+i:""),params:t.extend({},{_action:e.action},e.data)},e.method&&(o.params._method=e.method),void new Koowa.Form(o).submit())}}),Koowa.Controller.Form=Koowa.Controller.extend({_actionDefault:function(e){return e.validate&&!this.trigger("validate",[e])?!1:(this.form.append(t("<input/>",{name:"_action",type:"hidden",value:e.action})),this.trigger("submit",[e]),void this.form.submit())}})}(window.kQuery),window.jQuery=globalCacheForjQueryReplacement,globalCacheForjQueryReplacement=void 0;