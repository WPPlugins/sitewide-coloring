/*!
 * Micro Template Engine v0.1.3
 * https://github.com/cho45/micro-template.js/
 *
 * Based on John Resig's micro-templating http://ejohn.org/blog/javascript-micro-templating/
 *
 * Copyright cho45 cho45@lowreal.net https://www.lowreal.net/
 * Released under the MIT license
 * http://cho45.github.io/mit-license
 *
 * Date: 2016-04-13T19:30Z
 */
(function(factory) {
	factory(window.jQuery, window, document);
}(function($, window, document, undefined) {
	var Template = function (id, data) {
		var me = arguments.callee;
		if (!me.cache[id]) me.cache[id] = (function () {
			var name = id, string = /^[\w\-]+$/.test(id) ? me.get(id): (name = 'template(string)', id); // no warnings
			var line = 1, body = (
				"try { " +
				(me.variable ?  "var " + me.variable + " = this.stash;" : "with (this.stash) { ") +
				"this.ret += '"  +
				string.
				replace(/<%/g, '\x11').replace(/%>/g, '\x13'). // if you want other tag, just edit this line
				replace(/'(?![^\x11\x13]+?\x13)/g, '\\x27').
				replace(/^\s*|\s*$/g, '').
				replace(/\n|\r\n/g, function () { return "';\nthis.line = " + (++line) + "; this.ret += '\\n" }).
				replace(/\x11=raw(.+?)\x13/g, "' + ($1) + '").
				replace(/\x11=(.+?)\x13/g, "' + this.escapeHTML($1) + '").
				replace(/\x11(.+?)\x13/g, "'; $1; this.ret += '") +
				"'; " + (me.variable ? "" : "}") + "return this.ret;" +
				"} catch (e) { throw 'TemplateError: ' + e + ' (on " + name + "' + ' line ' + this.line + ')'; } " +
				"//@ sourceURL=" + name + "\n" // source map
			).replace(/this\.ret \+= '';/g, '');
			var func = new Function(body);
			var map  = { '&' : '&amp;', '<' : '&lt;', '>' : '&gt;', '\x22' : '&#x22;', '\x27' : '&#x27;' };
			var escapeHTML = function (string) { return (''+string).replace(/[&<>\'\"]/g, function (_) { return map[_] }) };
			return function (stash) { return func.call(me.context = { escapeHTML: escapeHTML, line: 1, ret : '', stash: stash }) };
		})();
		return data ? me.cache[id](data) : me.cache[id];
	};

	var ExtendedTemplate = function(id, data) {
		var fun = function (data) {
			data.include = function (name, args) {
				var stash = {};
				for (var key in Template.context.stash) if (Template.context.stash.hasOwnProperty(key)) {
					stash[key] = Template.context.stash[key];
				}
				if (args) for (var key in args) if (args.hasOwnProperty(key)) {
					stash[key] = args[key];
				}
				var context = Template.context;
				context.ret += Template(name, stash);
				Template.context = context;
			};

			data.wrapper = function (name, fun) {
				var current = Template.context.ret;
				Template.context.ret = '';
				fun.apply(Template.context);
				var content = Template.context.ret;
				var orig_content = Template.context.stash.content;
				Template.context.stash.content = content;
				Template.context.ret = current + Template(name, Template.context.stash);
				Template.context.stash.content = orig_content;
			};

			return Template(id, data);
		};

		return data ? fun(data) : fun;
	};

	$.extend(Template, {
		cache: {},
		extend: ExtendedTemplate,
		get: function (id) {
			var getTemplate = ExtendedTemplate.get;

			return getTemplate
				? getTemplate(id)
				: $('#' + id).html();
		}
	});

	$.microTpl = Template;

	$.fn.microTpl = function(data) {
		var tplWrapper = $(this);

		if ((tplWrapper.length == 1) && tplWrapper.is('script')) {
			if (tplWrapper.attr('type') && (tplWrapper.attr('type') == 'application/x-template')) {
				return Template(tplWrapper.attr('id'), data);
			}
		}
	}
}));