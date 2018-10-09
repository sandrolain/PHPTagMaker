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
	$closeTag   = NULL;

	if(!is_array($args[0]))
	{
		$tag    = array_shift($args);
		$attrs  = (is_array($args[0]) && !is_string($args[0][0]) && !is_array($args[0][0])) ? array_shift($args) : NULL;

		$tag    = trim($tag);

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
				$tagHTML .= ' id="' . $m[2] . '"';
			}

			if($m[3])
			{
				$tagHTML .= ' class="' . str_replace('.', ' ', $m[3]) . '"';
			}

			if($m[4])
			{
				$tagHTML .= $m[4];
			}
		}

		$tag    = mb_strtolower($tag);
		
		// Apply attributes
		if($attrs)
		{
			if(is_array($attrs))
			{
				foreach($attrs as $k => $v)
				{
					if($v === TRUE)
					{
						$v = $k;
					}

					if(is_string($v) || is_numeric($v))
					{
						$tagHTML .= " {$k}=" . (FALSE !== strpos($v, '"') ? "'{$v}'" : "\"{$v}\"");
					}
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