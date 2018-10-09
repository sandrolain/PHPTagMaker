<?php

/**
 * @author Sandro Lain
 */

namespace TagMaker;

function N()
{
	$autocloseTags  = ['area', 'base', 'br', 'embed', 'hr', 'iframe', 'img', 'input', 'link', 'meta', 'param', 'source', 'track'];

	$args       = func_get_args();
	$html       = [];
	$closeTag  	= NULL;

	if(!is_array($args[0]))
	{
		$tag    	= array_shift($args);
		$attrs  	= (is_array($args[0]) && !is_string($args[0][0]) && !is_array($args[0][0])) ? array_shift($args) : [];

		$tag		= trim($tag);
		$id			= NULL;
		$classNames	= NULL;

		// If is HTML
		if(preg_match('/^<([a-z0-9:_-]+)/i', $tag, $m))
		{
			$tagHTML    = $tag;
			$tag        = $m[1];

			$tagHTML    = preg_replace('/\/?>$/i', '', $tagHTML);
		}
		elseif(preg_match('/^([a-z0-9:_-]+)(?:#([a-z0-9_-]+))?(?:\.([a-z0-9\._-]+))?(\s+.+)?/i', $tag, $m))
		{
			$tag		= $m[1];

			$tagHTML    = "<{$tag}";

			if($m[2])
			{
				$id = $m[2];
			}

			if($m[3])
			{
				$classNames = trim(str_replace('.', ' ', $m[3]));
			}

			if($m[4])
			{
				$tagHTML .= $m[4];
			}
		}

		$tag    = mb_strtolower($tag);

		if($id && !$attrs['id'])
		{
			$attrs['id'] = $id;
		}

		if($classNames)
		{
			$attrs['class'] = $attrs['class'] ? $classNames . ' ' . $attrs['class'] : $classNames;
		}
		
		// Apply attributes
		if(is_array($attrs))
		{
			foreach($attrs as $k => $v)
			{
				if($v === TRUE)
				{
					$v = $k;
				}

				if(!is_string($v) && !is_numeric($v) && substr($k, 0, 5) == 'data-')
				{
					$v = json_encode($v);

					if($v !== FALSE)
					{
						$v = htmlspecialchars($v);
					}
				}

				if(is_string($v) || is_numeric($v))
				{
					$tagHTML .= " {$k}=" . (FALSE !== strpos($v, '"') ? "'{$v}'" : "\"{$v}\"");
				}
			}
		}

		$tagHTML    .= '>';

		$closeTag   = "</{$tag}>";
	}

	$childs = [];

	if($args)
	{
		foreach($args as $el)
		{
			switch(gettype($el))
			{
				case 'string':

					$childs[] = htmlspecialchars($el, ENT_HTML5, "UTF-8", FALSE);

				break;
				case 'integer':
				case 'double':

					$childs[] = $el;

				break;
				case 'array':

					$childs[] = call_user_func_array('\TagMaker\N', $el);

				break;
			}
		}
	}

	if($tagHTML)
	{
		if(!empty($childs))
		{
			$tagHTML    = preg_replace('/\/?>$/i', '', $tagHTML);
			$tagHTML    .= '>';

			$html[]     = $tagHTML;

			$html       = array_merge($html, $childs);
		}
		else
		{
			if(in_array($tag, $autocloseTags))
			{
				$tagHTML    = preg_replace('/\/?>$/', '', $tagHTML);
				$tagHTML    .= '/>';
				$closeTag   = NULL;
			}

			$html[]     = $tagHTML;
		}
		
		if($closeTag)
		{
			$html[]     = $closeTag;
		}
	}
	else
	{
		$html   = array_merge($html, $childs);
	}

	return implode('', $html);
}