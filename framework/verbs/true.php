<?php
/*
Fusebox Software License
Version 1.0

Copyright (c) 2003, 2004, 2005, 2006 The Fusebox Corporation. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted 
provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions 
  and the following disclaimer.

2. Redistributions in binary form or otherwise encrypted form must reproduce the above copyright 
  notice, this list of conditions and the following disclaimer in the documentation and/or other 
  materials provided with the distribution.

3. The end-user documentation included with the redistribution, if any, must include the following 
  acknowledgment:

  "This product includes software developed by the Fusebox Corporation (http://www.fusebox.org/)."

  Alternately, this acknowledgment may appear in the software itself, if and wherever such 
  third-party acknowledgments normally appear.

4. The names "Fusebox" and "Fusebox Corporation" must not be used to endorse or promote products 
  derived from this software without prior written (non-electronic) permission. For written 
  permission, please contact fusebox@fusebox.org.

5. Products derived from this software may not be called "Fusebox", nor may "Fusebox" appear in 
  their name, without prior written (non-electronic) permission of the Fusebox Corporation. For 
  written permission, please contact fusebox@fusebox.org.

If one or more of the above conditions are violated, then this license is immediately revoked and 
can be re-instated only upon prior written authorization of the Fusebox Corporation.

THIS SOFTWARE IS PROVIDED "AS IS" AND ANY EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED. IN NO EVENT SHALL THE FUSEBOX CORPORATION OR ITS CONTRIBUTORS BE LIABLE FOR ANY 
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR 
BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE 
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

-------------------------------------------------------------------------------

This software consists of voluntary contributions made by many individuals on behalf of the 
Fusebox Corporation. For more information on Fusebox, please see <http://www.fusebox.org/>.

*/
if ( $fb_['verbInfo']['executionMode'] == "start" ) {
	$fb_['cir'] =& $fb_['verbInfo']['action']->getCircuit();
	$fb_['app'] =& $fb_['cir']->getApplication();
	// validate attributes
	// <true> has no attributes
	// <true> must be nested inside an <if>
	if ( !array_key_exists("parent",$fb_['verbInfo']) || $fb_['verbInfo']['parent']['lexiconVerb'] != "if" ) {
		$this->fb_throw("fusebox.badGrammar.trueNeedsIf",
					"Verb 'true' has no parent 'if' verb",
					"Found 'true' verb with no parent 'if' verb in fuseaction {$fb_['verbInfo']['circuit']}.{$fb_['verbInfo']['fuseaction']}.");
	}	
	// strict mode - check attribute count:
	if ( $fb_['app']->strictMode ) {
		if ( Count($fb_['verbInfo']['attributes']) != 0 ) {
			$this->fb_throw("fusebox.badGrammar.unexpectedAttributes",
						"Unexpected attributes",
						"Unexpected attributes were found in a 'true' verb in fuseaction {$fb_['verbInfo']['circuit']}.{$fb_['verbInfo']['fuseaction']}.");
		}
	}
	
	// compile <true>
	// <true> is a no-op
}
?>