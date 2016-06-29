<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Minifyhtml Class
 * Will Minify the HTML. Reducing network latency, enhancing compression, and faster browser loading and execution.
 * 
 * @category	Output
 * @author      John Gerome
 * @link	    https://github.com/johngerome/CodeIgniter-Minifyhtml-hooks
 */
 
class Minifyhtml {
    
    /**
     * Responsible for sending final output to browser
     */
    function output()
    {
        $CI =& get_instance();
        $buffer = $CI->output->get_output();
        $re = '%# Collapse whitespace everywhere but in blacklisted elements.
        (?>             # Match all whitespans other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          [^<]*+        # Either zero or more non-"<" {normal*}
          (?:           # Begin {(special normal*)*} construct
            <           # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+      # more non-"<" {normal*}
          )*+           # Finish "unrolling-the-loop"
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre|script)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %Six';
        $buffer = preg_replace($re, " ", $buffer);
        $CI->output->set_output($buffer);
        $CI->output->_display();
    }
}
?>