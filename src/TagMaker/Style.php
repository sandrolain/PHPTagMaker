<?php

namespace TagMaker;

class Style implements \ArrayAccess
{
	protected $selector		= '';
	protected $rules		= [];
	protected $properties	= [];

	public function __construct(string $selector = '')
	{
		$this->setSelector($selector);
	}

	public function __get($name)
	{
		$this->getProperty($name);
	}

	public function __set($name, $value)
	{
		$this->setProperty($name, $value);
	}

	public function setSelector(string $selector = '')
	{
		$this->selector = $selector;
	}

	public function setPropertiesList(array $list)
	{
		foreach($list as $name => $value)
		{
			$this->setProperty($name, $value);
		}

		return $this;
	}

	public function setProperty(string $name, $value)
	{
		if(empty($name))
		{
			throw new Exception("Property name cannot be empty", 103);
		}

		$name = $this->hypenize($name);

		if(is_null($value) || $value === '')
		{
			unset($this->properties[$name]);

			return $this;
		}

		if(is_int($value) || is_double($value) || is_float($value))
		{
			if($value === 0)
			{
				$value = "0";
			}
			else
			{
				$value = "{$value}px";
			}
		}

		$this->properties[$name] = $value;

		return $this;
	}

	public function getProperty(string $name)
	{
		if(empty($name))
		{
			throw new Exception("Property name cannot be empty", 102);
		}

		$name = $this->hypenize($name);

		return $this->properties[$name];
	}

	public function rule(string $name)
	{
		if(empty($name))
		{
			throw new Exception("Rule name cannot be empty", 101);
		}

		if(!$this->rules[$name])
		{
			$this->rules[$name] = new Style($name);
		}
		
		return $this->rules[$name];
	}

	public function makeCSS(string $parentSelector = '', bool $minified = TRUE, string $subStr = "  ", int $sub = 0)
	{
		$rows	= [];

		$sel		= $this->selector;
		$selClose	= NULL;
		$tab		= $minified ? '' : str_repeat($subStr, $sub);
		$propTab	= $minified ? '' : str_repeat($subStr, $sub + 1);
		$s			= $minified ? '' : ' ';

		if($parentSelector)
		{
			if(strpos($parentSelector, '@media') !== FALSE)
			{
				$rows[] = "{$parentSelector}{$s}{";

				$selClose = "}";
			}
			else
			{
				$sel = "{$parentSelector} {$sel}";
			}
		}

		

		if(!empty($sel) && !empty($this->properties))
		{
			$rows[] = "{$tab}{$sel}{$s}{";

			foreach($this->properties as $key => $value)
			{
				$rows[] = "{$propTab}{$key}:{$s}{$value};";
			}

			$rows[] = "{$tab}}";
		}

		$subSub = $sub + (empty($sel) ? 0 : 1);

		foreach($this->rules as $name => $rule)
		{
			$rows[] = $rule->makeCSS($sel, $minified, $subStr, $subSub);
		}

		if($selClose)
		{
			$rows[] = $selClose;
		}

		return $minified ? implode("", $rows) : implode("\n", $rows);
	}

	public function makeStyleTag()
	{
		return "<style type=\"text/css\">\n" . $this->makeCSS() . "\n</style>";
	}

	public function __toString()
	{
		return $this->makeCSS();
	}

	protected function hypenize($str)
	{
		return mb_strtolower(preg_replace('/[A-Z]/', '-$0', $str));
	}

	// ArrayAccess Methods
	public function offsetSet($offset, $value)
	{
		if(is_string($offset) && is_array($value))
		{
			$this->rule($offset)->setPropertiesList($value);
		}
    }

	public function offsetExists($offset)
	{
        return isset($this->rules[$offset]);
    }

	public function offsetUnset($offset)
	{
        unset($this->rules[$offset]);
    }

	public function offsetGet($offset)
	{
        return $this->rule($offset);
    }
}