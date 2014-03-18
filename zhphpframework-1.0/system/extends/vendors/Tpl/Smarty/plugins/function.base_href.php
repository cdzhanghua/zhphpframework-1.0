<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function smarty_function_base_href($params, $template){

    return '<base href="'.HTTP_URL.APP_NAME.'/" />';
}
?>
