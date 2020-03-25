<?php
	include_once(dirname(__FILE__) ."/../adodb/adodb.inc.php");

  /**
  * Ignited Datatables ActiveRecords library for adodb database library
  *
  * @subpackage libraries
  * @category   library
  * @version    0.1
  * @author     Yusuf Ozdemir <yusuf@ozdemir.be>
  */

  class SQLHerper
  {
  	const debugQuery 	= FALSE;

    /**
    * Variables
    *
    */
    var $ar_select      = array();
    var $ar_from        = array();
    var $ar_join        = array();
	var $ar_groupby     = array();
    var $ar_where       = array();
    var $ar_orderby     = array();
    var $ar_limit       = FALSE;
    var $ar_offset      = FALSE;
    var $ar_order       = FALSE;

    var $_escape_char   = '`';
    var $_count_string  = 'SELECT COUNT(*) AS ';

    var $db ;
    var $_result;

    /**
    * Generates the SELECT portion of the query
    *
    */
    public function select($columns, $backtick_protect = TRUE)
    {
      foreach ($columns as $column)
        $this->ar_select[] = ($backtick_protect == TRUE)? $this->_protect_identifiers(trim($column)) : trim($column);

      return $this;
    }

    /**
    * Generates the FROM portion of the query
    *
    */
    public function from($from)
    {
      foreach ((array)$from as $f)
        $this->ar_from[] = $this->_protect_identifiers(trim($f));

      return $this;
    }

    /**
    * Generates the JOIN portion of the query
    *
    */
    public function join($table, $cond, $type = '')
    {
      if ($type != '')
      {
        $type = strtoupper(trim($type));
        $type = (!in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER')))? '':$type.' ' ;
      }

      $join = $type.'JOIN '.$this->_protect_identifiers($table).' ON '.$this->_protect_identifiers($cond);
      $this->ar_join[] = $join;

      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    */
    public function where($key, $value = NULL, $escape = TRUE, $type = 'AND ')
    {
      if ( ! is_array($key))
        $key = array($key => $value);

      foreach ($key as $k => $v)
      {
        $prefix = (count($this->ar_where) == 0)? '' : $type;

        if($v != NULL)
        {
          $k = ($this->_has_operator($k) == TRUE)? $k : $k . ' =';
          $v = ($escape == TRUE)? " '" . $v . "'" : $v;
        }

        $this->ar_where[] = $prefix . (($escape == TRUE)? $this->_protect_identifiers($k.$v) : $k.$v);
      }
      return $this;
    }

	    /**
    * Generates the JOIN portion of the query
    *
    */
    public function groupby($fields)
    {
      $groupby = $this->_protect_identifiers($fields);
      $this->ar_groupby[] = $groupby;

      return $this;
    }

    /**
    * Generates the LIMIT portion of the query
    *
    */
    public function limit($value, $offset = '')
    {
      $this->ar_limit = $value;

      if ($offset != '')
        $this->ar_offset = $offset;

      return $this;
    }

    /**
    * Generates the ORDER BY portion of the query
    *
    */
    public function order_by($orderby, $direction = '')
    {
    	$pos = strrpos($orderby, " ");
		if ($pos !== false)
			$orderby = substr($orderby,-$pos);
      	$direction = (in_array(strtoupper(trim($direction)), array('ASC', 'DESC'), TRUE))? ' '.$direction : ' ASC';
      	$this->ar_orderby[] = $orderby.$direction;

      	return $this;
    }

    /**
    * Runs the Query
    *
    */
    public function get()
    {
    	$db = NewADOConnection('mysqli');
		$db->Connect();
		$db->Execute("SET NAMES utf8;");
		$db->debug=self::debugQuery;

      	$result = $db->Execute($this->_compile_select()) or die($db->ErrorMsg());
      	$this->_reset_select();
      	$this->_result = $result;

      	return $this;
    }

	public function qstr($str)
	{
    	$db = NewADOConnection('mysqli');
		$db->Connect();
		$db->Execute("SET NAMES utf8;");
		return $db->qstr($str);
	}

    /**
    * Results as object
    *
    */
    public function result()
    {
      	$aData = array();
		while (!$this->_result->EOF)
		{
			$aData[] = $this->_result->fields;
			$this->_result->MoveNext();
		}

      	return $aData;
    }

    /**
    * Results as array
    *
    */
    public function result_array()
    {
      	$aData = array();
		while (!$this->_result->EOF)
		{
			$aData[] = $this->_result->GetRowAssoc(0);
			$this->_result->MoveNext();
		}

      	return $aData;
    }

    /**
    * Count Results
    *
    */
    public function count_all_results($table = '')
    {
    	if ($table != '')
        	$this->from($table);

      	$sql = $this->_compile_select($this->_count_string . 'numrows');
    	$db = NewADOConnection('mysqli');
		$db->Connect();
		$db->Execute("SET NAMES utf8;");
		$db->debug=self::debugQuery;

	  	$result = $db->Execute($sql) or die($db->ErrorMsg());
      	$this->_reset_select();

		if($result->RecordCount()>1)
      		return (int) $result->RecordCount();
		else
			return (int) $result->fields('numrows');
    }

    /**
    * Compile sql string
    *
    */
    protected function _compile_select($q = NULL)
    {
      $sql  = ($q == NULL)? 'SELECT DISTINCT ' : $q ;
      $sql .= implode(',', $this->ar_select);

      if(count($this->ar_from) > 0)
        $sql .= "\nFROM (".implode(',', $this->ar_from).")";

      if (count($this->ar_join) > 0)
	  	$sql .= "\n".implode("\n", $this->ar_join);

      if (count($this->ar_where) > 0)
        $sql .= "\nWHERE " . implode("\n", $this->ar_where);

	  if (count($this->ar_groupby) > 0)
        $sql .= "\nGROUP BY " . implode(", ", $this->ar_groupby);

      if (count($this->ar_orderby) > 0)// check
      {
        $sql .= "\nORDER BY " . implode(', ', $this->ar_orderby);
        if ($this->ar_order !== FALSE)
          $sql .= ($this->ar_order == 'desc')? ' DESC' : ' ASC';
      }

      if (is_numeric($this->ar_limit))
        $sql .= "\nLIMIT ".(($this->ar_offset == 0)? '' : $this->ar_offset.', ').$this->ar_limit;

      return $sql;
    }

    /**
    * Protect identifiers
    *
    */
    protected function _protect_identifiers($text)
    {
      $_pattern = '/\b(?<!"|\')(\w+)(?!\\1)\b/i';
      $item = preg_replace('/[\t ]+/', ' ', $text);
      $alias = '';

      if (strpos($item, ' ') !== FALSE)
      {
        $alias = strstr($item, " ");
        $item = substr($item, 0, - strlen($alias));
      }

      if (strpos($item, '(') !== FALSE)
        return $item.$alias;

      return preg_replace($_pattern, $this->_escape('$1'), $item).$alias;
    }

    /**
    * Test Operator
    *
    */
    protected function _has_operator($str)
    {
      return (!preg_match("/(\s|<|>|!|=|is null|is not null)/i", trim($str)))? FALSE : TRUE;
    }

    /**
    * Escape
    *
    */
    protected function _escape($text)
    {
      return $this->_escape_char . $text . $this->_escape_char ;
    }

    /**
    * Reset arrays
    *
    */
    protected function _reset_select()
    {
      $ar_reset_items = array(
        'ar_select'     => array(),
        'ar_from'       => array(),
        'ar_join'       => array(),
        'ar_where'      => array(),
        'ar_orderby'    => array(),
        'ar_limit'      => FALSE,
        'ar_offset'     => FALSE,
        'ar_order'      => FALSE
       );

      foreach ($ar_reset_items as $item => $default_value)
        $this->$item = $default_value;
    }
  }
/* End of file ActiveRecords.php */
