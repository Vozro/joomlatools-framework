/**
 * @version		$Id$
 * @category    Koowa
 * @package     Koowa_Media
 * @subpackage  Javascript
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

// needed for Table Column ordering
function KTableOrdering( order, dir, task ) 
{
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_direction.value	= dir;
	submitform( task );
}

function $get(key)
{
	if(key == "") return;
	
	 var uri   = location.search.parseUri();
	 var query = uri['query'].parseQueryString();
	 return query[key]; 
}	

String.extend({
 
	parseQueryString: function() 
	{
		var vars = this.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.each(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[keys[0]] = encodeURIComponent(keys[1]);
		});
		
		return rs;
	},
 
	parseUri: function()
	{
		var bits = this.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		return (bits)
			? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
			: null;
	}
});