<?php 
// -----------------------------------------------------------------------------
// PhpConcept Library - PclXml - pclxml.lib.php
// -----------------------------------------------------------------------------
// License GNU/GPL - Vincent Blavet - October 2008
// http://www.phpconcept.net
// -----------------------------------------------------------------------------
//   See http://www.phpconcept.net for documentation
// -----------------------------------------------------------------------------
// CVS : $Id$
// -----------------------------------------------------------------------------
  
  define('PCLXML_VERSION', '0.4');

  // ----- Error returns & Basic codes  
  define( 'PCLXML_ERR_NO_ERROR', 1 );
  define( 'PCLXML_ERR_ERROR', 0 );
  
  // ----- Error codes
  //   -1 : Unable to open file in binary write mode
  //   -2 : Unable to open file in binary read mode
  //   -3 : Invalid parameters
  //   -4 : File does not exist
  //   -5 : The XML file/string has syntax error
  //   -6 : No object in the hierarchy with this path
  //   -7 : No attribute with this name for the xml object
  //   -8 : No xml object with this path in the xml hierarchy
  define( 'PCLXML_ERR_WRITE_OPEN_FAIL', -1 );
  define( 'PCLXML_ERR_READ_OPEN_FAIL', -2 );
  define( 'PCLXML_ERR_INVALID_PARAMETER', -3 );
  define( 'PCLXML_ERR_MISSING_FILE', -4 );
  define( 'PCLXML_ERR_XML_SYNTAX_ERROR', -5 );
  define( 'PCLXML_ERR_INVALID_PATH', -6 );
  define( 'PCLXML_ERR_UNKNOWN_ATTRIBUTE', -7 );
  define( 'PCLXML_ERR_UNKNOWN_OBJECT', -8 );
  
  // ---------------------------------------------------------------------------
  // Class : PclXmlTag
  // Description :
  // Attributes :
  // Methods :
  // ---------------------------------------------------------------------------
  class PclXmlTag  {

    // ----- Name of the XML Tag
    protected $name;
    
    // ----- An array with the attributes of the XML Tag
    protected $att;
    
    // ----- An array of PclXmlTag object childs of the current Tag
    protected $childs;
    
    // ----- The value of the XML Tag
    protected $data;
  
    // ----- Working data
    // It's an array where working data can be set. The idea is to unset 
    // the unused rows to use less memory. Identified working data are :
    //   working_data['walk_index']
    //   working_data['walk_path']
    //   working_data['walk_child']
    //   working_data['token_list']
    //   working_data['error_code']
    //   working_data['error_message']
    protected $working_data;

    // -------------------------------------------------------------------------
    // Function : __construct()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function __construct()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::__construct", "");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $this->name = '';
      $this->att = array();
      $this->childs = array();
      $this->data = '';      
      $this->working_data = array();
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : error_reset()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function error_reset()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::error_reset", "");      
      $v_result = PCLXML_ERR_NO_ERROR;

      if (isset($this->working_data['error_code'])) {
        unset($this->working_data['error_code']);
      }
      if (isset($this->working_data['error_message'])) {
        unset($this->working_data['error_message']);
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : _error_set()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _error_set($p_code, $p_message)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_error_set", "code=".$p_code.", message='".$p_message."'");      
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $this->working_data['error_code'] = $p_code;
      $this->working_data['error_message'] = $p_message;
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : error_code()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function error_code()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::error_code", "");
      
      $v_result = PCLXML_ERR_NO_ERROR;
      if (isset($this->working_data['error_code'])) {
        $v_result = $this->working_data['error_code'];
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : error_name()
    // Description :
    // Parameters :
    // -------------------------------------------------------------------------
    function error_name($p_with_code=false)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::error_message", "");
      
      $v_error_code = PCLXML_ERR_NO_ERROR;
      if (isset($this->working_data['error_code'])) {
        $v_error_code = $this->working_data['error_code'];
      }
      
      $v_list = get_defined_constants();
      for (reset($v_list); $v_key = key($v_list); next($v_list)) {
  	  $v_prefix = substr($v_key, 0, 10);
  	  if ((   ($v_prefix == 'PCLXML_ERR')
           )
  	      && ($v_list[$v_key] == $v_error_code)) {
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_key);
            if ($p_with_code) {
              return($v_key.' ('.$v_error_code.')');
            }
            else {
              return($v_key);
            }
  	    }
      }
      
      $v_result = 'unknown';
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : error_message()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function error_message($p_full=false)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::error_message", "");
      
      $v_error_message = '';
      if (isset($this->working_data['error_message'])) {
        $v_error_message = $this->working_data['error_message'];
      }

      if ($p_full) {
        $v_result = $this->error_name(true)." : ".$v_error_message;
      }
      else {
        $v_result = $v_error_message;
      }

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : get_name()
    // Description :
    //   Get the name of the current PclXmlTag object.
    // Parameters :
    //   None.
    // Return Values :
    //   A string with the name.
    // -------------------------------------------------------------------------
    function get_name()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::get_name", "");
  
      $this->error_reset();
      $v_result = $this->name;
      
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : set_name()
    // Description :
    //   Set the name of the current PclXmlTag object. The p_name value must
    //   be a string.
    // Parameters :
    //   p_name : A string with the object name.
    // Return Values :
    //   PCLXML_ERR_NO_ERROR, on success,
    //   PCLXML_ERR_INVALID_PARAMETER, on error when p_name is not a string.
    // -------------------------------------------------------------------------
    function set_name($p_name)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::set_name", "name='".$p_name."'");
      $v_result = PCLXML_ERR_NO_ERROR;
  
      $this->error_reset();

      if (!is_string($p_name)) {
        $v_result = PCLXML_ERR_INVALID_PARAMETER;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_name, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      $this->name = $p_name;
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : walk_reset()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function walk_reset()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::walk_reset", "");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      if (   isset($this->working_data['walk_child']) 
          && ($this->working_data['walk_child'] !== 0)) {
        $this->working_data['walk_child']->walk_reset();
      }
      
      unset($this->working_data['walk_index']);
      unset($this->working_data['walk_path']);
      unset($this->working_data['walk_child']);
      
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : walk_childs()
    // Description :
    //   Return next child in the walk. p_path must be a 
    //   path to a valid xml sub tag or empty for current xml tag.
    // Samples :
    //   $v_xml->walk_reset();
    //   while (($v_result = $v_xml->walk_childs('core.mysql.host')) !== PCLXML_ERR_ERROR) {
    //     echo "Child name '".$v_result['name']."'<br>";    
    //   }
    // Parameters :
    // Return Values :
    //   An array with rows :
    //     result['name']=<name_of_the_child>
    //     result['child']=<reference_to_the_child_object>
    //   Returns PCLXML_ERR_ERROR(0) if not found or end of child list.
    // -------------------------------------------------------------------------
    function walk_childs($p_path='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::walk_childs", "path='".$p_path."'");
      $v_result = PCLXML_ERR_ERROR;
      
      $this->error_reset();

      if (!is_string($p_path)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_path, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      // ----- Look if not current walk with this path
      if (   !isset($this->working_data['walk_path'])
          || ($this->working_data['walk_path'] != $p_path)) {
        // ----- Reset the walk because new path
        $this->walk_reset();

        // ----- Store walk path
        $this->working_data['walk_path'] = $p_path;
      }
      
      // ----- Look for current object
      if ($p_path == '') {
        $this->working_data['walk_child'] = 0;
      }
      // ----- Look for subobject if not already in cache
      else if (!isset($this->working_data['walk_child'])) {
        $v_path_item = explode('.', $p_path);
        if (($this->working_data['walk_child'] = &$this->_get_child_recurrent($v_path_item)) === PCLXML_ERR_ERROR) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"No subobject with this path '".$p_path."'");
          $v_result = PCLXML_ERR_ERROR;
          $this->_error_set(PCLXML_ERR_UNKNOWN_OBJECT,
                            "Unknown object with path '".$p_path."'");
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
      else {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Subobject in cache or current object to use.");
      }
      
      // ----- Walk the attribute
      if ($this->working_data['walk_child'] === 0) {
        $v_result = $this->_walk_childs();
      }
      else {
        $v_result = $this->working_data['walk_child']->_walk_childs();
        if ($v_result === PCLXML_ERR_ERROR) {
          $this->_error_set($this->working_data['walk_child']->error_code(),
                            $this->working_data['walk_child']->error_message());
          $this->working_data['walk_child']->error_reset();
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : _walk_childs()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _walk_childs()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_walk_childs", "");
      $v_result = PCLXML_ERR_ERROR;
      
      if (!isset($this->working_data['walk_index'])) {
        $this->working_data['walk_index'] = 0;
      }
      
      if (isset($this->childs)) {
        $i=0;
        foreach ($this->childs as $v_key => $v_value) {
            if ($i == $this->working_data['walk_index']) {
              $v_result = array();
              $v_result['name'] = $this->childs[$v_key]->name;
              $v_result['child'] = &$this->childs[$v_key];
              $this->working_data['walk_index']++;
              break;
            }
            $i++;
        }
      }
      else {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"No childs in this object");
        $this->_error_set(PCLXML_ERR_UNKNOWN_OBJECT,
                          "No child in this object");
        $v_result = PCLXML_ERR_ERROR;
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : walk_attributes()
    // Description :
    //   Return next attribute name and value in the walk. p_path must be a 
    //   path to a valid xml sub tag or empty for current xml tag.
    // Samples :
    //   $v_xml->walk_reset();
    //   while (($v_result = $v_xml->walk_attributes('core.mysql.host')) !== PCLXML_ERR_ERROR) {
    //     echo "Attribute name '".$v_result['name']."', value '".$v_result['value']."'<br>";    
    //   }
    // Parameters :
    // Return Values :
    //   An array with rows :
    //     result['name']=<name>
    //     result['value']=<value>
    //   Where <name> and <value> are the researched values.
    //   Returns PCLXML_ERR_ERROR(0) if not found or end of attribute list.
    // -------------------------------------------------------------------------
    function walk_attributes($p_path='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::walk_attribute", "path='".$p_path."'");
      $v_result = PCLXML_ERR_ERROR;
      
      $this->error_reset();

      if (!is_string($p_path)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_path, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      // ----- Look if not current walk with this path
      if (!isset($this->working_data['walk_path']) || ($this->working_data['walk_path'] != $p_path)) {
        // ----- Reset the walk because new path
        $this->walk_reset();

        // ----- Store walk path
        $this->working_data['walk_path'] = $p_path;
      }
      
      // ----- Look for current object
      if ($p_path == '') {
        $this->working_data['walk_child'] = 0;
      }
      // ----- Look for subobject if not already in cache
      else if (!isset($this->working_data['walk_child'])) {
        $v_path_item = explode('.', $p_path);
        if (($this->working_data['walk_child'] = &$this->_get_child_recurrent($v_path_item)) === PCLXML_ERR_ERROR) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"No subobject with this path '".$p_path."'");
          $v_result = PCLXML_ERR_ERROR;
          $this->_error_set(PCLXML_ERR_UNKNOWN_OBJECT,
                            "Unknown object with path '".$p_path."'");
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
      else {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Subobject in cache or current object to use.");
      }
      
      // ----- Walk the attribute
      if ($this->working_data['walk_child'] === 0) {
        $v_result = $this->_walk_attributes();
      }
      else {
        $v_result = $this->working_data['walk_child']->_walk_attributes();
        if ($v_result === PCLXML_ERR_ERROR) {
          $this->_error_set($this->working_data['walk_child']->error_code(),
                            $this->working_data['walk_child']->error_message());
          $this->working_data['walk_child']->error_reset();
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : _walk_attributes()
    // Description :
    //   Return next attribute name and value in the walk. p_path must be a 
    //   path to a valid xml sub tag or empty for current xml tag. 
    // Parameters :
    // Return Values :
    //   An array with rows :
    //     result['name']=<name>
    //     result['value']=<value>
    //   Where <name> and <value> are the researched values.
    //   Returns 0 if not found or end of attribute list.
    // -------------------------------------------------------------------------
    protected function _walk_attributes()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_walk_attribute", "");
      $v_result = PCLXML_ERR_ERROR;
      
      if (!isset($this->working_data['walk_index'])) {
        $this->working_data['walk_index'] = 0;
      }
      
      if (isset($this->att)) {
        $i=0;
        foreach ($this->att as $v_key => $v_value) {
          if ($i == $this->working_data['walk_index']) {
            $v_result = array();
            $v_result['name'] = $v_key;
            $v_result['value'] = $v_value;
            $this->working_data['walk_index']++;
            break;
          }
          $i++;
        }
      }
      else {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"No attributes in this object");
        $this->_error_set(PCLXML_ERR_UNKNOWN_ATTRIBUTE,
                          "No attribute in this object");
        $v_result = PCLXML_ERR_ERROR;
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : get_attribute()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function get_attribute($p_path)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::get_attribute", "path='".$p_path."'");
      $v_result = PCLXML_ERR_ERROR;
      
      $this->error_reset();

      if (!is_string($p_path)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_path, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      // ----- Extract as an array
      $v_path_item = explode('.', $p_path);
      
      // ----- Remove last entry (which will be the attribute name requested)
      $v_attribute_name = array_pop($v_path_item);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Requested attribute is '".$v_attribute_name."'");
      
      // ----- Look if looking for an attribute of the current tag
      if (sizeof($v_path_item) == 0) {
        // ----- This is the current object
        if (isset($this->att[$v_attribute_name])) {
          $v_result = $this->att[$v_attribute_name];
        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"No attribute with name '".$v_attribute_name."'");
          $this->_error_set(PCLXML_ERR_UNKNOWN_ATTRIBUTE,
                        "Unknown attribute with name '".$v_attribute_name."'");
        }
      }
      else {
        // ----- This is a subobject
        if (($v_xmltag = &$this->_get_child_recurrent($v_path_item)) !== PCLXML_ERR_ERROR) {
          if (isset($v_xmltag->att[$v_attribute_name])) {
            $v_result = $v_xmltag->att[$v_attribute_name];
          }
          else {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"No attribute with name '".$v_attribute_name."' in object '".$v_xmltag->name."'");
            $this->_error_set(PCLXML_ERR_UNKNOWN_ATTRIBUTE,
                              "Unknown attribute with name '".$v_attribute_name."' in object '".$v_xmltag->name."'");
          }
        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"No object with this path '".$p_path."'");
          $this->_error_set(PCLXML_ERR_UNKNOWN_OBJECT,
                            "Unknown object with path '".$p_path."'");
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : set_attribute()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function set_attribute($p_path, $p_value)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::set_attribute", "path='".$p_path."'");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $this->error_reset();

      if (!is_string($p_path)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_path, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      // ----- Extract as an array
      $v_path_item = explode('.', $p_path);
      
      // ----- Remove last entry (which will be the attribute name requested)
      $v_attribute_name = array_pop($v_path_item);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Requested attribute is '".$v_attribute_name."'");
      
      // ----- Look if looking for an attribute of the current tag
      if (sizeof($v_path_item) == 0) {
        // ----- This is the current object
        if (isset($this->att[$v_attribute_name])) {
          $this->att[$v_attribute_name] = $p_value;
        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Attribute does not exist, create it");
          $this->att[$v_attribute_name] = $p_value;
/*          $v_result = PCLXML_ERR_ERROR;
          $this->_error_set(PCLXML_ERR_UNKNOWN_ATTRIBUTE,
                            "Unknown attribute name '".$v_attribute_name."'");
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
          */
        }
      }
      else {
        // ----- This is a subobject
        if (($v_xmltag = &$this->_get_child_recurrent($v_path_item)) !== 0) {
          if (isset($v_xmltag->att[$v_attribute_name])) {
            $v_xmltag->att[$v_attribute_name] = $p_value;
          }
          else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Attribute does not exist, create it");
            $v_xmltag->att[$v_attribute_name] = $p_value;
/*
            $v_result = PCLXML_ERR_ERROR;
            $this->_error_set(PCLXML_ERR_UNKNOWN_ATTRIBUTE,
                              "Unknown attribute name '".$v_attribute_name."' in object '".$v_xmltag->name."'");
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
            */
          }
        }
        else {
          $v_result = PCLXML_ERR_ERROR;
          $this->_error_set(PCLXML_ERR_UNKNOWN_OBJECT,
                            "Unknown xml object with this path ");
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : get_value()
    // Description :
    //   Get the value of an XML object. The object is identified by the 
    //   p_path parameter. If p_path is set to '' (empty string) then the
    //   returned value is the value of the current object.
    //   Default value for p_path is ''.
    // Parameters :
    // Return Values :
    //   A string with the value of the xml object
    //   or the error PCLXML_ERR_ERROR on error.
    // -------------------------------------------------------------------------
    function get_value($p_path='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::get_value", "path='".$p_path."'");
      $v_result = PCLXML_ERR_ERROR;
      
      $this->error_reset();

      if (!is_string($p_path)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_path, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      if ($p_path == '') {
        // ----- Get value of the current object
        $v_result = $this->data;
      }
      else {
        // ----- Extract as an array
        $v_path_item = explode('.', $p_path);
        
        // ----- This is a subobject
        if (($v_xmltag = &$this->_get_child_recurrent($v_path_item)) !== 0) {
            $v_result = $v_xmltag->data;
        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"No object with this path '".$p_path."'");
          $v_result = PCLXML_ERR_ERROR;
          $this->_error_set(PCLXML_ERR_UNKNOWN_OBJECT,
                            "Unknown object with path '".$p_path."'.");
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : set_value()
    // Description :
    // Parameters :
    // Return Values :
    //   PCLXML_ERR_NO_ERROR, on success,
    //   PCLXML_ERR_ERROR, on error.
    // -------------------------------------------------------------------------
    function set_value($p_value, $p_path='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::get_value", "path='".$p_path."'");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $this->error_reset();

      if (!is_string($p_value)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for $p_value, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      if (!is_string($p_path)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_path, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      if ($p_path == '') {
        // ----- Set value of the current object
        $this->data = $p_value;
      }
      else {
        // ----- Extract as an array
        $v_path_item = explode('.', $p_path);
        
        // ----- This is a subobject
        if (($v_xmltag = &$this->_get_child_recurrent($v_path_item)) !== 0) {
            $v_xmltag->data = $p_value;
        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"No object with this path '".$p_path."'");
          $v_result = PCLXML_ERR_ERROR;
          $this->_error_set(PCLXML_ERR_UNKNOWN_OBJECT,
                            "Unknown object with path '".$p_path."'.");
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : get_child()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function &get_child($p_path)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::get_child", "path='".$p_path."'");
      $v_result = PCLXML_ERR_ERROR;
      
      $this->error_reset();

      if (!is_string($p_path)) {
        $v_result = PCLXML_ERR_ERROR;
        $this->_error_set(PCLXML_ERR_INVALID_PARAMETER,
                          'Invalid type for p_path, string expected');
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      $v_path_item = explode('.', $p_path);
      $v_result = &$this->_get_child_recurrent($v_path_item);
      
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, "");
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : _get_child_recurrent()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function &_get_child_recurrent($p_path_item)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_get_child_recurrent", "");
  
      // ----- Extract token
      $v_token = $p_path_item[0];
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"Looking for token '".$v_token."'");
      
      // ----- Look for index to extract
      $v_index = 0;
      $v_index_list = explode('[', $v_token);
      if (sizeof($v_index_list) > 1) {
        $v_token = $v_index_list[0];
        $v_index_list2 = explode(']', $v_index_list[1]);
        $v_index = intval($v_index_list2[0]);
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"Token as index '".$v_index."' defined");
      }
      
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"Looking now for token '".$v_token."'");

      // ----- Extract the child
      $v_result = PCLXML_ERR_ERROR;
      $v_iteration = 0;
      foreach ($this->childs as $v_key => $v_child) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Look for child '".$v_key."'");
        if ($this->childs[$v_key]->name == $v_token) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Found matching name '".$v_token."'");
          if ($v_iteration == $v_index) {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Found matching index '".$v_index."'");
            $v_result = &$this->childs[$v_key];
          }
          else {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Not at the right index '".$v_iteration."' wait for next.");
            $v_iteration++;
          }
        }
      }
      
      // ----- Look for next
      if ($v_result !== PCLXML_ERR_ERROR) {
        if (sizeof($p_path_item) != 1) {
          array_shift($p_path_item);
          $v_result2 = &$v_result->_get_child_recurrent($p_path_item);
          if ($v_result2 === PCLXML_ERR_ERROR) {
            $this->_error_set($v_result->error_code(),
                              $v_result->error_message());
            $v_result->error_reset();
          }
          $v_result = &$v_result2;
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, "");
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : _parse_options()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _parse_options($p_options, $p_supported_options)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_parse_options", "");

      // ----- Array that will contain results      
      $v_options_result = array();

      // ----- Extract supported options
      // Format is : type:name=default_value
      // 'type' and 'default_value' can be omitted
      // Supported types are : 'int', 'str', 'bool'
      $v_options_list = array();
      $v_options_token = explode(',', $p_supported_options);
      foreach ($v_options_token as $v_option_key) {
        if ($v_option_key == '') {
          continue;
        }
        $v_option = array();
        $v_option['name'] = '';
        $v_option['type'] = 'str';
        $v_option['default_value'] = '';
        // ----- Extract type
        $v_explode_items = explode(':', $v_option_key, 2);
        if (sizeof($v_explode_items) == 2) {
          $v_option['type'] = trim($v_explode_items[0]);
          $v_value = trim($v_explode_items[1]);
        }
        else {
          $v_value = trim($v_explode_items[0]);
        }
        // ----- Extract name and default value
        $v_explode_items = explode('=', $v_value, 2);
        if (sizeof($v_explode_items) == 2) {
          $v_option['name'] = trim($v_explode_items[0]);
          $v_value = trim($v_explode_items[1]);
        }
        else {
          $v_option['name'] = trim($v_explode_items[0]);
          $v_value = '';
        }
        // ----- Format value
        switch ($v_option['type']) {
          case 'int' :
            $v_option['default_value'] = (integer)$v_value;
          break;
          case 'str' :
            $v_option['default_value'] = $v_value;
          break;
          case 'bool' :
            if ((strtolower($v_value) == 'true') || ($v_value == '1')) {
              $v_option['default_value'] = true;
            }
            else {
              $v_option['default_value'] = false;
            }
          break;
          default :
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Unknown option type : '".$v_option['type']."'");
            $v_option['type'] = 'str';
            $v_option['default_value'] = '';
        }
        
        $v_options_list[$v_option['name']] = $v_option;
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3,"Found option '".$v_option['name']."' of type '".$v_option['type']."', with default value '".$v_option['default_value']."'");
        
        // ----- Set the default value in the result tab
        $v_options_result[$v_option['name']] = $v_option['default_value'];
      }
      
      // ----- Parse options
      $v_options_values = explode(',', $p_options);
      
      foreach ($v_options_values as $v_option_value) {
        if ($v_option_value == '') {
          continue;
        }
        $v_option_items = explode('=', $v_option_value, 2);
        if (sizeof($v_option_items)!=2) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"Not a valid option");
          continue;
        }
        $v_name = trim($v_option_items[0]);
        if (isset($v_options_list[$v_name])) {
          //$v_options_result[$v_option_items[0]] = $v_option_items[1];

          switch ($v_options_list[$v_name]['type']) {
            case 'int' :
              $v_options_result[$v_name] = (integer)trim($v_option_items[1]);
            break;
            case 'str' :
              $v_options_result[$v_name] = trim($v_option_items[1]);
            break;
            case 'bool' :
              if (   (strtolower(trim($v_option_items[1])) == 'true')
                  || ($v_option_items[1] == '1')) {
                $v_options_result[$v_name] = true;
              }
              else {
                $v_options_result[$v_name] = false;
              }
            break;
          }
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"Set option '".$v_name."' = '".$v_options_result[$v_name]."'");

        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"Unsupported option '".$v_name."'");
        }
      }
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, "");
      return $v_options_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : read_file()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function read_file($p_filename, $p_options_string='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::read_file", "filename='".$p_filename."', options='".$p_options_string."'");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $this->error_reset();
      $this->working_data['token_list'] = array();
      
      // ----- Parse options
      $v_options = $this->_parse_options($p_options_string, 'bool:uppercase=false');
      
      $v_xml_parser = xml_parser_create();
      if (!$v_options['uppercase']) {
        xml_parser_set_option($v_xml_parser, XML_OPTION_CASE_FOLDING, 0);
      }
      xml_set_object($v_xml_parser, $this);
      xml_set_element_handler($v_xml_parser, "_parse_xml_start", "_parse_xml_end");
      xml_set_character_data_handler($v_xml_parser, "_parse_xml_data");
      
      if (!($fp = @fopen($p_filename, "r"))) {
        $this->_error_set(PCLXML_ERR_READ_OPEN_FAIL,
                          'Unable to open XML file "'.$p_filename.'" in read mode.');
        $v_result = PCLXML_ERR_READ_OPEN_FAIL;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      while ($data = @fread($fp, 4096)) {
        if (!xml_parse($v_xml_parser, $data, feof($fp))) {
          $this->_error_set(PCLXML_ERR_XML_SYNTAX_ERROR,
                            'XML error in file "'.$p_filename
                            .'" at line '.xml_get_current_line_number($v_xml_parser).' : \''
                            .xml_error_string(xml_get_error_code($v_xml_parser)).'\'');
          $v_result = PCLXML_ERR_XML_SYNTAX_ERROR;
          fclose($fp);
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
      xml_parser_free($v_xml_parser);
      
      @fclose($fp);
      unset($this->working_data['token_list']);
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : read_string()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function read_string($p_string, $p_options_string='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::read_string", "options='".$p_options_string."'");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $this->error_reset();
      $this->working_data['token_list'] = array();
      
      // ----- Parse options
      $v_options = $this->_parse_options($p_options_string, 'bool:uppercase=false');
      
      $v_xml_parser = xml_parser_create();
      if (!$v_options['uppercase']) {
        xml_parser_set_option($v_xml_parser, XML_OPTION_CASE_FOLDING, 0);
      }
      xml_set_object($v_xml_parser, $this);
      xml_set_element_handler($v_xml_parser, "_parse_xml_start", "_parse_xml_end");
      xml_set_character_data_handler($v_xml_parser, "_parse_xml_data");
  
      if (!xml_parse($v_xml_parser, $p_string)) {
        $this->_error_set(PCLXML_ERR_XML_SYNTAX_ERROR,
                          'XML error in string '
                          .' at line '.xml_get_current_line_number($v_xml_parser).' : \''
                          .xml_error_string(xml_get_error_code($v_xml_parser)).'\'');
        $v_result = PCLXML_ERR_XML_SYNTAX_ERROR;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      xml_parser_free($v_xml_parser);
      unset($this->working_data['token_list']);
      
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : _parse_xml_start()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _parse_xml_start($parser, $p_tagname, $p_attributes)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_parse_xml_start", "tagname='".$p_tagname."'");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      // ----- Create a new tag
      $v_tag = new PclXmlTag();
      
      // ----- Transform attributes keys in lowercase
      $v_tag->att = $p_attributes;
      $v_tag->name = $p_tagname;
      
      // ----- Add the tag in the depth list
      $i = sizeof($this->working_data['token_list']);
      $this->working_data['token_list'][$i] = &$v_tag;
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : _parse_xml_data()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _parse_xml_data($parser, $p_data)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_parse_xml_data", "");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $i = sizeof($this->working_data['token_list']);
      
      if ($i>0) {
        $i--;
        $p_data = trim($p_data);
        if ($this->working_data['token_list'][$i]->data == '') {
          $this->working_data['token_list'][$i]->data = $p_data;
        }
        else if ($p_data != '') {
          $this->working_data['token_list'][$i]->data .= ' '.$p_data;
        }
      }
      else {
        // Ignore : blanks outside a tag
      }
  
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : _parse_xml_end()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _parse_xml_end($parser, $p_tagname)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_parse_xml_end", "name='".$p_tagname."'");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      // ----- Search for last token in the depth list
      $i = sizeof($this->working_data['token_list'])-1;
      if ($i<0) {
        // TBC : Should never goes here : XML syntax error
        // XML parser should have detected this before.
        $v_result = 0;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result, 'invalid empty list');
        return $v_result;
      }
      
      // ----- Get the latest / current xml tag
      $v_pclxmltag = &$this->working_data['token_list'][$i];
      
      // ----- Check that it is the same name
      if ($p_tagname != $v_pclxmltag->name) {
        // TBC : Should never occur : XML syntax error
        $v_result = 0;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result, 'invalid tag name mismatch');
        return $v_result;
      }
      
      // ----- Look for parent or child property
      // If $i is 0 then this is the first object of the xml file (parent)
      // If not then this is one child of the precedent object in the 
      // depth list.
      if ($i == 0) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Last/First object in the tag list");
        $this->name = $v_pclxmltag->name;
        $this->att = $v_pclxmltag->att;
        $this->childs = $v_pclxmltag->childs;
        $this->data = $v_pclxmltag->data;        
      }
      else {
        // ----- Get parent tag
        $v_parent = &$this->working_data['token_list'][$i-1];
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5,"Parent tag is : '".$v_parent->name."'");
        $v_parent->childs[] = $v_pclxmltag;
      }
      
      // ----- Remove the token from the depth list
      unset($this->working_data['token_list'][$i]);
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
    
    // -------------------------------------------------------------------------
    // Function : write_file()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    function write_file($p_filename, $p_options_string='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::write_file", "filename='".$p_filename."', options='".$p_options_string."'");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      $this->error_reset();

      // ----- Parse options
      $v_options = $this->_parse_options($p_options_string, 'bool:xmlheader=false,bool:newline=false,int:indent=0,int:indent_start=0');
      
      if (!($fd = @fopen($p_filename, "w"))) {
        $this->_error_set(PCLXML_ERR_WRITE_OPEN_FAIL,
                          'Unable to open XML file "'.$p_filename.'" in write mode.');
        $v_result = PCLXML_ERR_WRITE_OPEN_FAIL;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      
      $v_result = $this->_write_file($fd, $v_options);
      
      @fclose($fd);
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : _write_file()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _write_file($fd, $p_options)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::write_file", "");
      $v_result = PCLXML_ERR_NO_ERROR;

      $v_indent = 0;
      if ($p_options['indent']>0) {
        $p_options['newline']=true;
        $v_indent = $p_options['indent_start'];
        $p_options['indent_start'] += $p_options['indent'];
      }
      
      // ----- Look for XML header
      if ($p_options['xmlheader']) {
        fwrite($fd, '<?xml version="1.0" encoding="UTF-8"?'.'>');
        if ($p_options['newline']) {
          fwrite($fd, "\r\n");
        }
        $p_options['xmlheader']=false;
      }

      // ----- Write tag header
      $this->_write_indent($fd, $v_indent);
      fwrite($fd, '<'.$this->name);
      
      // ----- Write tag attributes
      foreach ($this->att as $v_name => $v_value) {
        fwrite($fd, ' '.$v_name.'="'.$v_value.'"');     
      }
      
      // ----- End of tag header
      fwrite($fd, '>');

      // ----- Write childs
      foreach ($this->childs as &$v_tag) {
        if ($p_options['newline']) {
          fwrite($fd, "\r\n");
        }
        $v_tag->_write_file($fd, $p_options);
      }
      
      // ----- Write data
      if ($p_options['newline'] && (sizeof($this->childs)!=0)) {
        fwrite($fd, "\r\n");
      }
      fwrite($fd, $this->data);
      if ($p_options['newline'] && (sizeof($this->childs)!=0) && ($this->data != '')) {
        fwrite($fd, "\r\n");
      }
      if ($p_options['newline'] && (sizeof($this->childs)!=0)) {
        $this->_write_indent($fd, $v_indent);
      }
      
      // ----- Write tag footer
      fwrite($fd, '</'.$this->name.'>');
      
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : _write_indent()
    // Description :
    // Parameters :
    // Return Values :
    // -------------------------------------------------------------------------
    protected function _write_indent($fd, $p_indent)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclXmlTag::_write_indent", "");
      $v_result = PCLXML_ERR_NO_ERROR;
      
      for ($i=0; $i<$p_indent; $i++) {
        fwrite($fd, " ");
      }

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }
    // -------------------------------------------------------------------------
  }
  // ---------------------------------------------------------------------------

?>