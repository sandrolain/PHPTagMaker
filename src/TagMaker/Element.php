<?php

/**
 * @author Sandro Lain
 */

namespace TagMaker;

class Element implements \ArrayAccess
{
	protected $name			= 'div';
	protected $attributes	= [];
	protected $children		= [];
	
	public static $selfclosedTags = ['area', 'base', 'br', 'embed', 'hr', 'iframe', 'img', 'input', 'link', 'meta', 'param', 'source', 'track'];
	
	public function __construct(string $name = 'div', array $attributes = [], $children = NULL)
	{
		$this->setName($name);

		$this->setAttributesList($attributes);

		$this->addChildren($children);
	}

	public function __set($name, $value)
	{
		$this->setAttribute($name, $value);
	}
	
	public function __get($name)
	{
		$this->getAttribute($name);
	}

	public function setName(string $name)
	{
		$this->name = mb_strtolower($name);

		return $this;
	}
	
	public function getAttribute(string $attribute)
	{
		return $this->attributes[$attribute];
	}
	
	public function setAttribute(string $attribute, $value = '')
	{
		$this->attributes[$attribute] = $value;

		return $this;
	}

	public function setAttributesList(array $attributes)
	{
		foreach($attributes as $name => $value)
		{
			$this->setAttribute($name, $value);
		}

		return $this;
	}
	
	public function removeAttribute($name)
	{
		if(isset($this->attributes[$name]))
		{
			unset($this->attributes[$name]);
		}

		return $this;
	}
	
	public function removeAllAttributes()
	{
		$this->attributes = [];

		return $this;
	}
	
	public function addChildren($child)
	{
		$childs = func_get_args();

		foreach($childs as $child)
		{
			if($child !== NULL)
			{
				$this->children[] = $child;
			}
		}

		return $this;
	}

	public function getArray()
	{
		$arr = [$this->name];

		if(!empty($this->attributes))
		{
			$arr[] = $this->attributes;
		}

		$arr = array_merge($arr, $this->children);

		return $arr;
	}
	
	public function build()
	{
		return Maker::makeHTML($this);
	}

	public function __toString()
	{
		return $this->build();
	}

	// ArrayAccess Methods
	public function offsetSet($offset, $value)
	{
		if (is_null($offset))
		{
            $this->children[] = $value;
		}
		else
		{
            $this->children[$offset] = $value;
        }
    }

	public function offsetExists($offset)
	{
        return isset($this->children[$offset]);
    }

	public function offsetUnset($offset)
	{
        unset($this->children[$offset]);
    }

	public function offsetGet($offset)
	{
        return isset($this->children[$offset]) ? $this->children[$offset] : NULL;
    }
}