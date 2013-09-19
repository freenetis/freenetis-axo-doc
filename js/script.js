var lang = __t('lang');

/**
 * Translate function
 * 
 * @param {String} text
 * @returns {String}
 */
function __t(text)
{
	if (typeof text !== 'undefined')
	{
		lc = text.toLowerCase();

		if (lc in _t)
		{
			text = _t[lc];
		}
	}
	
	for (i = 0; i < arguments.length-1; i++)
	{
		text = text.replace('{s}', '<span class="important">'+arguments[i+1]+'</span>');
	}
	
	return text;
}

/**
 * Shows details for selected node
 * 
 * @param {Object} node
 */
function onActivate(node)
{
	if (node.parent !== null)
	{
		var parent = node.parent;

		// display breadcrumbs
		var breadcrumbs = new Array();
		breadcrumbs.unshift(node.data.nf_title);
		
		while (parent.parent !== null)
		{
			breadcrumbs.unshift("<span class='breadcrumbs' href='"+parent.data.key+"'>"+parent.data.title+"</span>");
			parent = parent.parent;
		}
		
		$('#details #breadcrumbs').html(breadcrumbs.join(' > '));
		
		// display title
		$('#details h2').html(node.data.title);
		
		// display subtitle
		switch (node.data.type)
		{
			case 'controller':
				$('#subtitle').html(
					__t(
						'{s} is controller which contains following pages:',
						node.data.nf_title
					)
				);
				break;
			case 'method':
				$('#subtitle').html(
					__t(
						'{s} is page in {s} controller which requires following access rights:',
						node.data.nf_title,
						node.parent.data.nf_title
					)
				);
				break;
			case 'axo':
				$('#subtitle').html(
					__t(
						'{s} is access right with following properties:',
						node.data.nf_title
					)
				);
				break;
			default:
				if (typeof node.data.type !== 'undefined')
				{
					var count = node.data.type.match(/\//g);

					if (count === null)
					{
						$('#subtitle').html(
							__t(
								'{s} is AXO section with following AXO values:',
								node.data.nf_title
							)
						);
					}
					else if (count.length === 1)
					{
						$('#subtitle').html(
							__t(
								'{s} is AXO value in {s} AXO section used on following pages:',
								node.data.nf_title,
								node.parent.data.nf_title
							)
						);
					}
					else if (count.length === 2)
					{
						$('#subtitle').html(
							__t(
								'{s} AXO value in {s} AXO section is used on {s} page',
								node.parent.data.nf_title,
								node.parent.parent.data.nf_title,
								node.data.nf_title
							)
						);
					}
				}
				else
				{
					$('#subtitle').text('');
				}
		}		

		// show detail link
		if (node.data.links === 'controller')
		{
			$('#details #show_detail').html("<span class='detail_link links' href='controller/"+node.data.nf_title+"'>"+__t('Show used access rights on this page')+"</span>");
			$('#details #show_detail').removeClass('hide');
		}
		else
		{
			$('#details #show_detail').addClass('hide');
		}
		
		// display details from children
		var detail = '';
		if (node.data.children !== null &&
			node.data.type !== 'method')
		{
			len = node.data.children.length;
			
			for (i = 0; i < len; i++)
			{
				detail += '<li><span class="links detail_link" href="'+node.data.children[i].key+'">'+node.data.children[i].nf_title+"</span></li>";
			}
			
			$('#details #node_detail').html('<ul>'+detail+'</ul>');
			$('#details #node_detail').removeClass('hide');
		}
		else
		{
			$('#details #node_detail').addClass('hide');
		}
		
		// display method details
		if (node.data.type === 'method')
		{
			$('#details #method_detail').removeClass('hide');
			
			len = node.data.children.length;
			
			$('#access_table tr.axo').remove();
			$('#access-partial_table tr.axo').remove();
			$('#links_table tr.axo').remove();
			$('#breadcrumbs_table tr.axo').remove();
			$('#grid-action_table tr.axo').remove();
			
			for (i = 0; i < len; i++)
			{
				var title = node.data.children[i].value;
				var cls = '';
				
				if (typeof node.data.children[i].c_en !== 'undefined' &&
					node.data.children[i].c_en !== null &&
					typeof node.data.children[i].c_cs !== 'undefined' &&
					node.data.children[i].c_cs !== null
					)
				{
					if (lang === 'cs')
					{
						var comment = node.data.children[i].c_cs;
					}
					else
					{
						var comment = node.data.children[i].c_en;
					}
					
					title = '<span class="has_hint" title="'+comment+'">'+title+'</span>';
				}
				
				
				$('#'+node.data.children[i].usage+"_table").append('<tr class="axo"><td>'+title+'</td><td>'+node.data.children[i].section+'</td><td>'+node.data.children[i].action+'</td><td><span class="detail_link axo_show_detail" href="axo_section/'+node.data.children[i].section+'/'+node.data.children[i].value+'"></span></td></tr>');
			}
		}
		else
		{
			$('#details #method_detail').addClass('hide');
		}
		
		// display AXO details
		if (node.data.type === 'axo')
		{
			$('#details #axo').text("");
			$('#details #aco').text("");
			$('#details #usage').text("");

			$('#details #axo').html("<span class='detail_link links' href='axo_section/"+node.data.section+"/"+node.data.value+"'>"+node.data.value+"</span> (<span class='detail_link links' href='axo_section/"+node.data.section+"'>"+node.data.section+"</span>)");
			$('#details #aco').text(node.data.action+" ("+__t(node.data.action)+")");
			$('#details #usage').text(__t(node.data.usage));
			$('#details #axo_detail').removeClass('hide');
		}
		else
		{
			$('#details #axo_detail').addClass('hide');
		}

		// display comment
		if (typeof node.data.c_en !== 'undefined' &&
			node.data.c_en !== null &&
			typeof node.data.c_cs !== 'undefined' &&
			node.data.c_cs !== null
			)
		{
			if (lang === 'cs')
			{
				var comment = node.data.c_cs;
			}
			else
			{
				var comment = node.data.c_en;
			}
			
			$('#details #comment').text("");
			$('#details #comment').text(comment);
			
			$('#details #comments').removeClass('hide');
		}
		else
		{
			$('#details #comments').addClass('hide');
		}
	}
}

/**
 * Custom renderer for dynatree nodes
 * 
 * @param {Object} node
 * @returns {String}
 */
function customRender(node)
{
	if (lang === 'cs')
	{
		comment = node.data.c_cs;
	}
	else
	{
		comment = node.data.c_en;
	}
	
	if (typeof comment !== 'undefined' &&
			comment !== null)
	{
		content = comment;
	}
	else
	{
		content = null;
	}
	
	if (node.data.icon === false)
	{
		node.data.title = '<span class="inactive">'+__t(node.data.title)+'</span>';
	}
	
	if (content)
	{
		content = node.data.title+"<p class='inactive'>"+content+"</p>";
	}
	else
	{
		content = node.data.title;
	}
	
	return '<a class="dynatree-title" href="#">'+content+'</a>';
}

/**
 * Initialize page
 */
$(function()
{
	//translate GUI
	$('.t').text(function(){
		$(this).text(__t($(this).text()));
	});
	
	// download data
	$.ajax({
		url: "source.php",
		success: function(msg) {
			var data = JSON.parse(msg);

			// init trees
			$('#sources_tree').dynatree({
				debugLevel: 0,
				children: data.sources,
				onActivate: onActivate,
				onCustomRender: customRender
			});

			$('#sections_tree').dynatree({
				debugLevel: 0,
				children: data.sections,
				onActivate: onActivate,
				onCustomRender: customRender
			});

			// redirect to requested page/axo
			var href = window.location.search;

			$('#loading').addClass('hide');
			$('#header, #content-padd').removeClass('hide');

			// trigger window resize
			$(window).resize();

			$('#sources_tab').click();

			if (typeof href.split("?")[1] !== 'undefined')
			{
				query_string = decodeURIComponent(href.split("?")[1]);
				query_string = query_string.split("=")
				tab = query_string[0];
				req = query_string[1];

				if (tab === 'axo')
				{
					$('#axo_sections_tab').click();
					$('#sections_tree').dynatree('getTree').activateKey(tab+'/'+req);
					$('#sections_tree').dynatree('getActiveNode').expand();
				}
				else
				{
					$('#sources_tab').click();
					$('#sources_tree').dynatree('getTree').activateKey(tab+'/'+req);
					$('#sources_tree').dynatree('getActiveNode').expand();
				}
			}
		},
		error: function() {
			$('body').text(__t('Cannot download database.'));
			$('body').addClass('error');
		}
	});
});

$('span.breadcrumbs').live('click', function() {
	if ($('#sources_tab').hasClass('tab_link'))
	{
		$('#sections_tree').dynatree('getTree').activateKey($(this).attr('href'));
	}
	else
	{
		$('#sources_tree').dynatree('getTree').activateKey($(this).attr('href'));
	}
});

// AXO click handler
$('span.detail_link').live('click', function() {
	href = $(this).attr('href');
	
	if (href.indexOf('axo_section/') === 0)
	{
		$('#axo_sections_tab').click();

		$('#sections_tree').dynatree('getTree').activateKey('_0');
		$('#sections_tree').dynatree('getTree').activateKey($(this).attr('href'));

		$('#sections_tree').dynatree('getActiveNode').expand();
	}
	else
	{
		$('#sources_tab').click();
	
		$('#sources_tree').dynatree('getTree').activateKey('_0');
		$('#sources_tree').dynatree('getTree').activateKey($(this).attr('href'));

		$('#sources_tree').dynatree('getActiveNode').expand();
	}
});

// Sources tab click handler
$('#sources_tab').live('click', function() {
	$('#sections_tree').addClass('hide');
	$('#sources_tree').removeClass('hide');
	$('#sources_tab').removeClass('tab_link');
	$('#axo_sections_tab').addClass('tab_link');
	$('#tab_help').text(__t('Tree structure represents page address in FreenetIS. E.g. members - show_all corresponds to members/show_all page in FreenetIS.'));
});

// Sections tab click handler
$('#axo_sections_tab').live('click', function() {
	$('#sources_tree').addClass('hide');
	$('#sections_tree').removeClass('hide');
	$('#sources_tab').addClass('tab_link');
	$('#axo_sections_tab').removeClass('tab_link');
	$('#tab_help').text(__t('Tree structure represents access rights structure and pages where is selected access right used.'));
});

$(window).resize(function(){
	$('#content, ul.dynatree-container').css('height', ($(window).height() - $('#header').height() - 6));
});