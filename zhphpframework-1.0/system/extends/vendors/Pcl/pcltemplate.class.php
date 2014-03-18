<?php
// -----------------------------------------------------------------------------
// PhpConcept Template Engine - pcltemplate.class.php
// -----------------------------------------------------------------------------
// License GNU/LGPL - Vincent Blavet - November 2006
// http://www.phpconcept.net
// -----------------------------------------------------------------------------
// Overview :
//   See http://www.phpconcept.net/pcltemplate for more information
// -----------------------------------------------------------------------------
// CVS : $Id$
// -----------------------------------------------------------------------------
  
  // ----- Global Constants
  define('PCL_TEMPLATE_VERSION', '0.5-RC1');
  define('PCL_TEMPLATE_START', '<!--(');
  define('PCL_TEMPLATE_STOP', ')-->');
  
  // ----- Error codes
  define( 'PCL_TEMPLATE_ERR_NO_ERROR', 1 );
  define( 'PCL_TEMPLATE_ERR_GENERIC', 0 );
  define( 'PCL_TEMPLATE_ERR_SYNTAX', -1 );
  define( 'PCL_TEMPLATE_ERR_READ_OPEN_FAIL', -2 );
  define( 'PCL_TEMPLATE_ERR_WRITE_OPEN_FAIL', -3 );
  define( 'PCL_TEMPLATE_ERR_INVALID_PARAMETER', -4 );

  // ---------------------------------------------------------------------------
  // Class : PclTemplate
  // Description :
  // Attributes :
  // Methods :
  // ---------------------------------------------------------------------------
  class PclTemplate
  {
    // ----- $template_name
    // Filename of the template. When the template is not a string.
    var $template_name;
    
    // ----- $token_start & $token_stop
    // The tokens delimiters. They have default values. But can be changed
    // dynamically.
    var $token_start;
    var $token_stop;
    
    // ----- $tokens
    // The recursive array that contain the template in memory after parsing
    // The format of this array is :
    // tokens[]['type'] : Type of the token. The valid types are :
    //                    'line' : A text beetwen 2 tokens
    //                    'token' : A single reference to remplace by the real 
    //                              value.
    //                    'list' : A part of the template that will be 
    //                             associated to an array
    //                    'item' : A part of the template that will be repeated
    //                             for each element of an array
    //                    'ifempty' : A part of the template, associated to an
    //                                array that will be used if the array is
    //                                empty.
    //                    'ifnotempty' : A part of the template, associated to 
    //                                   an array that will be used if the
    //                                   array is not empty.
    //                    'if' : A part of the template that will be used if a
    //                           condition matches.
    //                    'ifnot' : A part of the template that will be used if
    //                              a condition does not match.
    // tokens[]['name'] : The name of the token to identify it. The same name
    //                    will be used in that structure that will fill the 
    //                    template.
    // tokens[]['text'] : The text that follow the token (same as 'line').    
    // tokens[]['tokens'] : Array of childs tokens
    var $tokens;
    
    // ----- Internal error handling
    // error_list[]['code'] : The error code.
    // error_list[]['text'] : The associated error string.
    // error_list[]['date'] : The associated error time and date.
    var $error_list;
    
    // -------------------------------------------------------------------------
    // Function : PclTemplate()
    // Description :
    // -------------------------------------------------------------------------
    function PclTemplate()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::PclTemplate', '');

      $this->template_name = '';
      $this->tokens = array();
      $this->token_start = PCL_TEMPLATE_START;
      $this->token_stop = PCL_TEMPLATE_STOP;
      
      $this->error_list = 0;
      
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 1);
      return;
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : errorInfo()
    // Description :
    // -------------------------------------------------------------------------
    function errorInfo()
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::errorInfo', '');
      
      $v_text = '';
      
      if (!isset($this->error_list) || (!is_array($this->error_list))) {
        $v_text = $this->_error_name(PCL_TEMPLATE_ERR_NO_ERROR)."(".PCL_TEMPLATE_ERR_NO_ERROR.")"; 
      }
      else {
        foreach ($this->error_list as $v_error) {
          $v_text .= $this->_error_name($v_error['code'])."(".$v_error['code'].") : ".$v_error['text']." (".$v_error['date'].")\n"; 
        }
      }
      $v_text = trim($v_text);

      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_text);
      return($v_text);
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : changeDelimiters()
    // Description :
    //   Change the delimiters strings used in the template file.
    //   No coherency check is done with the used string. The user
    //   must be sure that the delimiters are coherent.
    // -------------------------------------------------------------------------
    function changeDelimiters($p_start_delimiter, $p_stop_delimiter)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::changeDelimiters', 'start="'.$p_start_delimiter.'", stop="'.$p_stop_delimiter.'"');
      
      $this->token_start = $p_start_delimiter;
      $this->token_stop = $p_stop_delimiter;

      $v_result=1;
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : parseFile()
    // Description :
    // Return values :
    //   PCL_TEMPLATE_ERR_NO_ERROR : on success.
    //   PCL_TEMPLATE_ERR_READ_OPEN_FAIL : unable to open the template file in 
    //                                     read mode.
    //   PCL_TEMPLATE_ERR_SYNTAX : template syntax error.
    //   PCL_TEMPLATE_ERR_GENERIC : other errors.
    // -------------------------------------------------------------------------
    function parseFile($p_template)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::parseFile', 'template="'.$p_template.'"');
      $v_result = 1;
      
      // ----- Reset logs
      $this->_error_reset();
      
      // ----- Store the current template filename
      $this->template_name = $p_template;
      
      // ----- Try to open the template file in read mode
      $handle = @fopen($this->template_name, "r");
      if (!$handle) {
        $v_result = 0;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result, "Unable to open '".$this->template_name."'");
        return($v_result);
      }

      // ----- Start the template reading with an empty array of tokens.
      // Call the recursive parsing method to fill the list of tokens or
      // blocks.
      $v_buffer = '';
      $v_start_token = array();
      $v_line_number = 0;
      $v_result = $this->_parse_recursive($v_start_token, $v_buffer, $v_line_number, $handle);
      @fclose($handle);
      if ($v_result != 1) {
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return($v_result);
      }
      
      // ----- Store the list in the PclTemplate object
      $this->tokens = $v_start_token['tokens'];
      
      $v_result=1;
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : parseString()
    // Description :
    // -------------------------------------------------------------------------
    function parseString($p_string)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::parseString', '');
      $v_result = 1;
      
      // ----- Reset logs
      $this->_error_reset();

      $v_start_token = array();
      $v_line_number = 0;
      $v_result = $this->_parse_recursive($v_start_token, $p_string, $v_line_number);
      if ($v_result != 1) {
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return($v_result);
      }
      
      $this->tokens = $v_start_token['tokens'];
      
      $v_result=1;
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------
  
    // -------------------------------------------------------------------------
    // Function : _parse_recursive()
    // Description :
    // Return values :
    //   PCL_TEMPLATE_ERR_NO_ERROR : on success.
    //   PCL_TEMPLATE_ERR_SYNTAX : template syntax error.
    //   PCL_TEMPLATE_ERR_GENERIC : other errors.
    // -------------------------------------------------------------------------
    function _parse_recursive(&$p_token, &$p_buffer, &$p_line, $p_fd=0)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::_parse_recursive', '');
      $v_result = 1;
      
      // ----- Initialize
      if (isset($p_token['tokens']) && is_array($p_token['tokens'])) {
        $v_token_list = $p_token['tokens'];
        $v_current_index = sizeof($v_token_list);
      }
      else {
        $v_token_list = array();
        $v_current_index = 0;
      }
      
      $v_token_list[$v_current_index]['type'] = 'line';
      $v_token_list[$v_current_index]['text'] = '';
      
      do {
        // ----- Look if no token delimiter in the current line
        // which means the line is a single text and we add it
        // in the current text token
        if (($v_pos = strpos($p_buffer,$this->token_start)) === FALSE) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Line has no token");
          $v_token_list[$v_current_index]['text']  .= $p_buffer; 
          $p_buffer = '';          
        }
        
        // ----- The buffer has a start delimiter
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Token found at '".$v_pos."'");
          $v_token_list[$v_current_index]['text']  .= substr($p_buffer, 0, $v_pos);
          $v_pos += strlen($this->token_start);
          $p_buffer = substr($p_buffer, $v_pos);
          ////--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Start token : '".htmlentities($p_buffer)."'");
          $v_pos2 = strpos($p_buffer, $this->token_stop);
          ////--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Line : '".htmlentities($p_buffer)."' end at ".$v_pos2);
          //$v_token = strtolower(substr($p_buffer, $v_pos, $v_pos2-$v_pos));     
          $v_token = strtolower(substr($p_buffer, 0, $v_pos2));     
          $v_pos2 += strlen($this->token_stop);
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "token is '".htmlentities($v_token)."'");
          $p_buffer = substr($p_buffer, $v_pos2);
          
          // ----- Parse the token structure & Create the new token
          $v_current_index++;
          if (strpos($v_token, ":") === FALSE) {
            // ----- Look if $v_token is a reserved keyword
            // If not then by default the type is 'token',
            // If yes, then it is a token with no name
            if ($this->_is_keyword($v_token)) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "token is reserved keyword");
              $v_token_list[$v_current_index]['type'] = $v_token;
              $v_token_list[$v_current_index]['name'] = '';
              $v_tok = $v_token;
            }
            else {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "value is not a reserved keyword, type is set to 'token'");
              $v_token_list[$v_current_index]['type'] = 'token';
              $v_token_list[$v_current_index]['name'] = $v_token;
              $v_tok = 'token';
            }
          }
          else {
            // ----- Separate token type from token name
            list($v_tok,$v_name) = explode(":", $v_token);
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "short token is '".htmlentities($v_tok)."'");
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "short name is '".htmlentities($v_name)."'");
            $v_token_list[$v_current_index]['type'] = $v_tok;
            $v_token_list[$v_current_index]['name'] = $v_name;
          }
            
          switch ($v_tok) {
            case 'token' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
            break;

            case 'include' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
            break;

            case 'list.start' :
            case 'list' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
              $v_token_list[$v_current_index]['type'] = 'list';
              $v_result = $this->_parse_recursive($v_token_list[$v_current_index], $p_buffer, $p_line, $p_fd);
              if ($v_result != 1) {
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
            break;

            case 'list.stop' :
            case 'endlist' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'list')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected list parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'endlist' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }

              // ----- Not a new token, but end of current list
              unset($v_token_list[$v_current_index]);
              $p_token['tokens'] = $v_token_list;                
              $v_result=1;
              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
              return($v_result);      
            break;

            case 'list.empty.start' :
            case 'ifempty' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'list')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected list parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'ifempty' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
              $v_token_list[$v_current_index]['type'] = 'ifempty';
              $v_result = $this->_parse_recursive($v_token_list[$v_current_index], $p_buffer, $p_line, $p_fd);
              if ($v_result != 1) {
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
            break;

            case 'list.empty.stop' :
            case 'endifempty' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'ifempty')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected empty parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'endifempty' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }

              // ----- Not a new token, but end of current list
              unset($v_token_list[$v_current_index]);
              $p_token['tokens'] = $v_token_list;                
              $v_result=1;
              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
              return($v_result);      
            break;

            case 'list.notempty.start' :
            case 'ifnotempty' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'list')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected list parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'ifnotempty' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
              $v_token_list[$v_current_index]['type'] = 'ifnotempty';
              $v_result = $this->_parse_recursive($v_token_list[$v_current_index], $p_buffer, $p_line, $p_fd);
              if ($v_result != 1) {
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
            break;

            case 'list.notempty.stop' :
            case 'endifnotempty' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'ifnotempty')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected ifnotempty parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'endifnotempty' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }

              // ----- Not a new token, but end of current list
              unset($v_token_list[$v_current_index]);
              $p_token['tokens'] = $v_token_list;                
              $v_result=1;
              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
              return($v_result);      
            break;

            case 'list.item.start' :
            case 'item' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'list')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected list parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'item' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
              $v_token_list[$v_current_index]['type'] = 'item';
              $v_result = $this->_parse_recursive($v_token_list[$v_current_index], $p_buffer, $p_line, $p_fd);
              if ($v_result != 1) {
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
            break;

            case 'list.item.stop' :
            case 'enditem' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'item')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected item parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'enditem' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }

              // ----- Not a new token, but end of current list
              unset($v_token_list[$v_current_index]);
              $p_token['tokens'] = $v_token_list;                
              $v_result=1;
              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
              return($v_result);      
            break;

            case 'if.start' :
            case 'if' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
              $v_token_list[$v_current_index]['type'] = 'if';
              $v_result = $this->_parse_recursive($v_token_list[$v_current_index], $p_buffer, $p_line, $p_fd);
              if ($v_result != 1) {
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
            break;

            case 'if.stop' :
            case 'endif' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'if')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected 'if' parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'endif' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }

              // ----- Not a new token, but end of current if
              unset($v_token_list[$v_current_index]);
              $p_token['tokens'] = $v_token_list;                
              $v_result=1;
              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
              return($v_result);      
            break;

            case 'ifnot.start' :
            case 'ifnot' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");          
              $v_token_list[$v_current_index]['type'] = 'ifnot';
              $v_result = $this->_parse_recursive($v_token_list[$v_current_index], $p_buffer, $p_line, $p_fd);
              if ($v_result != 1) {
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }
            break;

            case 'ifnot.stop' :
            case 'endifnot' :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Valid token type '".$v_tok."' found");
              // ----- Check that the parent token is a list with same name
              if ((!isset($p_token['type'])) || ($p_token['type'] != 'ifnot')) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Template parse error : expected 'ifnot' parent");
                $this->_error_log(PCL_TEMPLATE_ERR_SYNTAX, "Parsing error : unexpected token 'endifnot' in file '".$this->template_name."' line ".$p_line);
                $v_result=PCL_TEMPLATE_ERR_SYNTAX;
                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
                return($v_result);      
              }

              // ----- Not a new token, but end of current if
              unset($v_token_list[$v_current_index]);
              $p_token['tokens'] = $v_token_list;                
              $v_result=1;
              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
              return($v_result);      
            break;

            default :
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Unknown token type '".$v_tok."', replacing by token");          
              $v_token_list[$v_current_index]['type'] = 'token';
          }
        
          // TBC : can be removed ... A token other than line may not have text
          $v_token_list[$v_current_index]['text'] = '';

          $v_current_index++;
          $v_token_list[$v_current_index]['type'] = 'line';
          $v_token_list[$v_current_index]['text'] = '';

          ////--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "current line : '".htmlentities($v_current_line)."'");
        }
        
        if (($p_buffer == '') && ($p_fd != 0)) {
          $p_buffer = fgets($p_fd, 4096);
          $p_line++;
        }
        
      } while (   ($p_buffer != '') && ($p_buffer !== FALSE)
               && (($p_fd == 0) || (!feof($p_fd))));
      
      $p_token['tokens'] = $v_token_list;
      
      $v_result=1;
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------  

    // -------------------------------------------------------------------------
    // Function : generate()
    // Description :
    // Arguments :
    //   $p_output : 'stdout', 'file', 'string'
    //   $p_filename : filename when 'file' is used in $p_output
    // Return Values :
    //   a string when $p_output='string' and no error
    //   PCL_TEMPLATE_ERR_NO_ERROR : If no error.
    //   0 : on error.
    // -------------------------------------------------------------------------
    function generate($p_struct, $p_output='stdout', $p_filename='')
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::generate', 'filename="'.$p_filename.'"');
      $v_result = 1;
      
      $fd = 0;
      if ($p_output == 'file') {
        if (!($fd = @fopen($p_filename, "w"))) {
          $v_result = PCL_TEMPLATE_ERR_WRITE_OPEN_FAIL;
          $this->_error_log(PCL_TEMPLATE_ERR_WRITE_OPEN_FAIL, "Unable to open file '".$p_filename."' in write mode.");
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result, 'unable to open file "'.$p_filename.'"');
          return($v_result);
        }
      }
      
      $v_result = $this->_generate($this->tokens, $p_struct, $p_output, $fd);

      if ($fd != 0) {
        @fclose($fd);
      }
      
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------
    
    // -------------------------------------------------------------------------
    // Function : _generate()
    // Description :
    // Arguments :
    //   $p_output : 'stdout', 'file', 'string'
    //   $p_fd : file descriptor when $p_output='file'
    // -------------------------------------------------------------------------
    function _generate($p_token_list, $p_struct, $p_output='stdout', $p_fd=0)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::_generate', '');
      $v_result = '';
      $v_global_result = '';
      
      foreach ($p_token_list as $v_token) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Look for token type '".$v_token['type']."'");
        $v_string = '';
        switch ($v_token['type']) {
          case 'line' :
            $v_string = $v_token['text'];
          break;
          case 'token' :
            // ----- Search for list with matching name
            $v_value = $this->_find_token($v_token['name'], $p_struct);
            
            // ----- Check that value is a string
            if (is_string($v_value)) {
              // ----- Add the value
              $v_string = $v_value;
            }
            else if ($v_value !== FALSE) {
              // ----- Add the value
              $v_string = (string)$v_value;
            }
            else {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Invalid value for token '".$v_token['name']."'");
            }
          break;
          case 'if' :
            // ----- Search for list with matching name
            $v_value = $this->_find_token($v_token['name'], $p_struct);
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "look for ".$v_token['type']." condition");
            
            // ----- A token with this name is found
            if ($v_value !== FALSE) {
              // ----- Look for other than an array
              // If the value is an array then the condition is a "condition
              // block". Recursive call.
              // If the value is not an array then the condition is a single
              // condition. We need to change this to a condition block with
              // a single value inside. 
              if (!is_array($v_value)) {
                if (is_string($v_value)) {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Value is a string");
                  $v_string = $v_value;
                }
                else {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Value is cast to a string");
                  $v_string = (string)$v_value;
                }
                $v_value = array();
                $v_value[$v_token['name']] = $v_string;
              }

              $v_string = $this->_generate($v_token['tokens'], $v_value, $p_output, $p_fd);
            }
            else {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "No value for token '".$v_token['name']."'");
            }
          break;
          case 'ifnot' :
            // ----- Search for list with matching name
            $v_value = $this->_find_token($v_token['name'], $p_struct);
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "look for ".$v_token['type']." condition");
            
            // ----- Look if found
            // If found, all the block is ignored.
            // If not found then the recursive call is done with the same 
            // struct
            if ($v_value === FALSE) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Token ifnot not found. Call recursive with same struct.");
              $v_string = $this->_generate($v_token['tokens'], $p_struct, $p_output, $p_fd);
            }
            else {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "No value for token '".$v_token['name']."'; Skip ifnot block.");
            }
          break;
          case 'list' :
            // ----- Search for list with matching name
            $v_value = $this->_find_token($v_token['name'], $p_struct);
            
            // ----- Check that value is an array
            if (is_array($v_value)) {
//              $v_string = $this->_generate_list($v_token['tokens'], $v_value, $p_output, $p_fd);
              $v_string = $this->_generate($v_token['tokens'], $v_value, $p_output, $p_fd);
            }
            else {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Invalid value for token '".$v_token['name']."', or empty list");
              //$v_string = $this->_generate_list($v_token['tokens'], array(), $p_output, $p_fd);
            }
          break;
          case 'ifempty' :
            if (sizeof($p_struct) == 0) {
              $v_value = $this->_find_token($v_token['name'], $p_struct);
              if (is_array($v_value)) {
                $v_string = $this->_generate($v_token['tokens'], $v_value, $p_output, $p_fd);
              }
              else {
                $v_string = $this->_generate($v_token['tokens'], array(), $p_output, $p_fd);
              }
            }
          break;
          case 'ifnotempty' :
            if (sizeof($p_struct) > 0) {
              $v_value = $this->_find_token($v_token['name'], $p_struct);
              if (is_array($v_value)) {
                $v_string = $this->_generate($v_token['tokens'], $v_value, $p_output, $p_fd);
              }
              else {
                $v_string = $this->_generate($v_token['tokens'], array(), $p_output, $p_fd);
              }
            }
          break;
          case 'item' :
            $v_string = '';
            foreach ($p_struct as $v_elt) {
              $v_temp_str = $this->_generate($v_token['tokens'], $v_elt, $p_output, $p_fd);
              if ($v_temp_str === 0) {
                break;
              }         
              $v_string .= $v_temp_str;     
            }
          break;
          case 'include' :
            // ----- Search for include informations with matching name
            $v_value = $this->_find_token($v_token['name'], $p_struct);
            
            // ----- Check that value is an array
            if (   is_array($v_value)
                && (isset($v_value['filename']))) {
              if (isset($v_value['values'])) {
                $v_tokens_inc = $v_value['values'];
              }
              else {
                $v_tokens_inc = array();
              }
              $v_string = $this->_generate_include($v_value['filename'],
                                                   $v_tokens_inc,
                                                   $p_output, $p_fd);              
            }
            else {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Invalid value for token '".$v_token['name']."' specific array expected");
              // TBC : error management ...
            }
          break;
          default :
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "No rule to manage '".$v_token['type']."'");
          break;
        }
        
        if (!is_string($v_string)) {
          $v_result = 0;
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return($v_result);
        }
      
        switch ($p_output) {
          case 'stdout' :
            echo $v_string;
          break;
          case 'file' :
            @fwrite($p_fd, $v_string);
          break;
          case 'string' :
            $v_global_result .= $v_string;
          break;
          default :
            $this->_error_log(PCL_TEMPLATE_ERR_INVALID_PARAMETER, "Unsupported parameter '".$p_output."'");
            $v_result = 0;
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return($v_result);
          break;
        }
      }

      if ($p_output == 'string') {
        $v_result = $v_global_result;
      }
      else {
        $v_result = PCL_TEMPLATE_ERR_NO_ERROR;
      }
      
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------
    
    // -------------------------------------------------------------------------
    // Function : _generate_include()
    // Description :
    // Arguments :
    //   $p_output : 'stdout', 'file', 'string'
    //   $p_fd : file descriptor when $p_output='file'
    // -------------------------------------------------------------------------
    function _generate_include($p_filename, $p_struct, $p_output='stdout', $p_fd=0)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::_generate_include', "filename='".$p_filename."'");
      $v_result = '';
      
      // ----- Create the template object
      $v_template = new PclTemplate();
      
      // ----- Parse the template file
      if (($v_result = $v_template->parseFile($p_filename)) != PCL_TEMPLATE_ERR_NO_ERROR) {
        $this->error_list = array_merge($this->error_list,
                                        $v_template->error_list);
        $v_result = 0;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return($v_result);
      }

      // ----- Generate result
      $v_result = $v_template->_generate($v_template->tokens, $p_struct,
                                         $p_output, $p_fd);
      
      // ----- Unset
      unset($v_template);

      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Function : _find_token()
    // Description :
    // Arguments :
    // -------------------------------------------------------------------------
    function _find_token($p_token_name, $p_struct)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::_find_token', 'token="'.$p_token_name.'"');
      
      if (!is_array($p_struct)) {
        $v_result = FALSE;
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result, "argument is not an array");
        return($v_result);
      }
      
      if (isset($p_struct[$p_token_name])) {
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 1, "found by direct index");
          return($p_struct[$p_token_name]);
      }
      
      foreach ($p_struct as $v_key => $v_item) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Look for value with key '".$v_key."'");
        if (strtolower($v_key) == $p_token_name) {
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 1, "found by strtolower");
          return($v_item);
        }
      }

      $v_result = FALSE;
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result, "not found");
      return($v_result);
    }
    // -------------------------------------------------------------------------
    
    // -------------------------------------------------------------------------
    // Function : _is_keyword()
    // Description :
    // Arguments :
    // -------------------------------------------------------------------------
    function _is_keyword($p_token)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclTemplate::_is_keyword', 'token="'.$p_token.'"');
      $v_pcltemplate_keywords = array(
                                  'token',
                                  'list', 'endlist',
                                  'item', 'enditem',
                                  'ifnotempty', 'endifnotempty',
                                  'ifempty', 'endifempty',
                                  'if', 'endif',
                                  'ifnot', 'endifnot',
                                  'include');
      $v_result = in_array($p_token, $v_pcltemplate_keywords);
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return($v_result);
    }
    // -------------------------------------------------------------------------
    
    // --------------------------------------------------------------------------------
    // Function : _error_name()
    // Description :
    // Parameters :
    // --------------------------------------------------------------------------------
    function _error_name($p_code)
    {
      $v_name = array (  PCL_TEMPLATE_ERR_NO_ERROR => 'PCL_TEMPLATE_ERR_NO_ERROR'
                        ,PCL_TEMPLATE_ERR_GENERIC => 'PCL_TEMPLATE_ERR_GENERIC'
                        ,PCL_TEMPLATE_ERR_SYNTAX => 'PCL_TEMPLATE_ERR_SYNTAX'
                        ,PCL_TEMPLATE_ERR_READ_OPEN_FAIL => 'PCL_TEMPLATE_ERR_READ_OPEN_FAIL'
                        ,PCL_TEMPLATE_ERR_WRITE_OPEN_FAIL => 'PCL_TEMPLATE_ERR_WRITE_OPEN_FAIL'
                      );

      if (isset($v_name[$p_code])) {
        $v_value = $v_name[$p_code];
      }
      else {
        $v_value = $p_code.'(NoName)';
      }
  
      return($v_value);
    }
    // --------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------
    // Function : _error_log()
    // Description :
    // Parameters :
    // --------------------------------------------------------------------------------
    function _error_log($p_error_code=PCL_TEMPLATE_ERR_NO_ERROR, $p_error_string='')
    {
      if (   (!isset($this->error_list))
          || (!is_array($this->error_list))
          || (sizeof($this->error_list) == 0)) {
        $this->error_list = array();
        $v_index = 0;
      }
      else {
        $v_index = sizeof($this->error_list);
      }
      
      $this->error_list[$v_index]['code'] = $p_error_code;
      $this->error_list[$v_index]['text'] = $p_error_string;
      $this->error_list[$v_index]['date'] = date('d/m/Y H:i:s');
      
    }
    // --------------------------------------------------------------------------------
  
    // --------------------------------------------------------------------------------
    // Function : _error_reset()
    // Description :
    // Parameters :
    // --------------------------------------------------------------------------------
    function _error_reset()
    {
      unset($this->error_list);
      $this->error_list = 0;
    }
    // --------------------------------------------------------------------------------
  
  }
  // ---------------------------------------------------------------------------
?>
