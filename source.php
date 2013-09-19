<?php
/*
 * This file is part of open source system FreenetIS
 * and it is released under GPLv3 licence.
 * 
 * More info about licence can be found:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * More info about project can be found:
 * http://www.freenetis.org/
 * 
 */

/**
 * Rebuilds associative array to dynatree array format
 * 
 * @param array $nodes
 * @param string $prev
 * @return array
 */
function rebuild($nodes, $prev = null)
{
	ksort($nodes);
	$root = array();
	
	foreach ($nodes AS $n => $v)
	{
		// rebuild children
		$children = rebuild($v, ($prev !== null ? "$prev/$n" : $n));
		
		$icon = null;
		// Set icon if list
		if (!is_array($v))
		{
			$icon = "$v.png";
		}
		
		// create node
		$node = array(
			'title' => $n,
			'nf_title' => $n,
			'isFolder' => is_array($v),
			'icon' => $icon,
			'links' => $v,
			'key' => ($prev !== null ? "$prev/$n" : $n),
			'children' => $children,
			'type' => $prev
		);
		
		$root[] = $node;
	}
	
	return $root;
}

// load XML
$file = file_get_contents('axo_doc.xml');

if ($file === FALSE)
{
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	die;
}

$xml = new XMLReader($file);

$xml->XML($file);

$controllers = array();
$helpers = array();
$libraries = array();
$views = array();
$sections = array();

// parse XML
while ($xml->read())
{
	if ($xml->nodeType == XMLReader::ELEMENT)
	{
		if ($xml->name == 'object')
		{
			$object = array();
			$object['title'] = $xml->getAttribute('name');
			$object['nf_title'] = $xml->getAttribute('name');
			$object['c_en'] = $xml->getAttribute('comment-en');
			$object['c_cs'] = $xml->getAttribute('comment-cs');
			$object['isFolder'] = true;
			$object['type'] = $xml->getAttribute('type');
			$object['hide'] = $xml->getAttribute('hide');
			$object['key'] = $xml->getAttribute('type')."/".$xml->getAttribute('name');
			$object['children'] = array();
		}
		else if ($xml->name == 'method')
		{
			$method = array();
			$method['title'] = $xml->getAttribute('name');
			$method['nf_title'] = $xml->getAttribute('name');
			$method['c_en'] = $xml->getAttribute('comment-en');
			$method['c_cs'] = $xml->getAttribute('comment-cs');
			$method['isFolder'] = true;
			$method['type'] = 'method';
			$method['key'] = $object['key']."/".$xml->getAttribute('name');
			$method['children'] = array();
		}
		else if ($xml->name == 'axo')
		{
			$axo = array();
			$own = '_all';
			
			if ($xml->getAttribute('own') == 'true')
			{
				$own = '_own';
			}
			
			$f_title = $title = $xml->getAttribute('section')." - ".$xml->getAttribute('value')." - ".$xml->getAttribute('action').$own;
			
			switch ($xml->getAttribute('usage_type'))
			{
				case 'access':
					$icon = 'access.png';
					$f_title = "<span class='access'>$title</span>";
					break;
				case 'access-partial':
					$icon = 'access-partial.png';
					break;
				case 'links':
					$icon = 'links.png';
					break;
				case 'grid-action':
					$icon = 'grid-action.png';
					break;
				case 'breadcrumbs':
					$icon = 'breadcrumbs.png';
					break;
				default:
					$icon = null;
			}
			
			$axo['title'] = $f_title;
			$axo['nf_title'] = $title;
			$axo['section'] = $xml->getAttribute('section');
			$axo['value'] = $xml->getAttribute('value');
			$axo['action'] = $xml->getAttribute('action').$own;
			$axo['usage'] = $xml->getAttribute('usage_type');
			$axo['icon'] = $icon;
			$axo['type'] = 'axo';
			$axo['links'] = 'axo';
			$axo['key'] = $method['key']."/".$title;
			
			
			if (!isset($sections[$xml->getAttribute('section')]))
			{
				$sections[$xml->getAttribute('section')] = array();
			}
			
			if (!isset($sections[$xml->getAttribute('section')][$xml->getAttribute('value')]))
			{
				$sections[$xml->getAttribute('section')][$xml->getAttribute('value')] = array();
			}
				
			if ($object['type'] == 'controller')
			{
				$sections[$xml->getAttribute('section')][$xml->getAttribute('value')][$object['title']."/".$method['title']] = $object['type'];
			}
		}
		else if ($xml->name == 'comment')
		{
			$axo['c_'.$xml->getAttribute('lang')] = $xml->readString();
		}
	}
	else if ($xml->nodeType == XMLReader::END_ELEMENT)
	{
		if ($xml->name == 'object' && $xml->getAttribute('type') == 'controller')
		{
			if ($object['hide'] !== 'true')
			{
				$controllers[] = $object;
			}
		}
		if ($xml->name == 'object' && $xml->getAttribute('type') == 'helper')
		{
			$helpers[] = $object;
		}
		if ($xml->name == 'object' && $xml->getAttribute('type') == 'library')
		{
			$libraries[] = $object;
		}
		if ($xml->name == 'object' && $xml->getAttribute('type') == 'view')
		{
			$views[] = $object;
		}
		else if ($xml->name == 'method')
		{
			// do not show __construct method if has no AXOs
			if (!(empty($method['children']) &&
				$method['title'] === '__construct'
				))
			{
				// show message if method has no AXOs
				if (empty($method['children']))
				{
					$method['children'] = array(
						'title' => 'This page has no access rights',
						'icon' => false
					);
				}

				// add method to object
				$object['children'][] = $method;
			}
		}
		else if ($xml->name == 'axo')
		{
			// add AXO to method
			if ($object['type'] !== 'view')
			{
				$method['children'][] = $axo;
			}
			else
			{
				$object['children'][] = $axo;
			}
		}
	}
}

// rebuild AXO sections
$axos = rebuild($sections, 'axo_section');

$data = array();
$data['sources'] = $controllers;

// merge sections
/*$data['sources'][] = array(
	'title' => 'Controllers',
	'isFolder' => true,
	'children' => $controllers
);

$data['sources'][] = array(
	'title' => 'Helpers',
	'isFolder' => true,
	'children' => $helpers
);

$data['sources'][] = array(
	'title' => 'Libraries',
	'isFolder' => true,
	'children' => $libraries
);

$data['sources'][] = array(
	'title' => 'Views',
	'isFolder' => true,
	'children' => $views
);*/

$data['sections'] = $axos;

// encode to json format
echo json_encode($data);