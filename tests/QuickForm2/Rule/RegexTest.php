<?php
/**
 * Unit tests for HTML_QuickForm2 package
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2006, 2007, Alexey Borzov <avb@php.net>,
 *                           Bertrand Mansion <golgote@mamasam.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   HTML
 * @package    HTML_QuickForm2
 * @author     Alexey Borzov <avb@php.net>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_QuickForm2
 */

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Rule checking that the form field is not empty
 */
require_once 'HTML/QuickForm2/Rule/Regex.php';

/**
 * Element class
 */
require_once 'HTML/QuickForm2/Element.php';

/**
 * Unit test for HTML_QuickForm2_Rule_Regex class 
 */
class HTML_QuickForm2_Rule_RegexTest extends PHPUnit_Framework_TestCase
{
    public function testRegexIsRequired()
    {
        $mockEl = $this->getMock('HTML_QuickForm2_Element', array('getType', 
                                 'getValue', 'setValue', '__toString'));
        $regex  = new HTML_QuickForm2_Rule_Regex($mockEl, 'some error');
        try {
            $regex->validate();
        } catch (HTML_QuickForm2_Exception $e) {
            $this->assertRegexp('/Regex Rule needs a regular expression/', $e->getMessage());
            return;
        }
        $this->fail('Expected HTML_QuickForm2_Exception was not thrown');
    }

    public function testOptionsHandling()
    {
        $mockEl = $this->getMock('HTML_QuickForm2_Element', array('getType', 
                                 'getValue', 'setValue', '__toString'));
        $mockEl->expects($this->exactly(2))->method('getValue')
               ->will($this->returnValue('foo123'));

        $alpha = new HTML_QuickForm2_Rule_Regex($mockEl, 'an error', '/^[a-zA-Z]+$/');
        $this->assertFalse($alpha->validate());

        $alphaNum = new HTML_QuickForm2_Rule_Regex($mockEl, 'an error', '/^[a-zA-Z0-9]+$/');
        $this->assertTrue($alphaNum->validate());
    }

    public function testConfigHandling()
    {
        $mockEl  = $this->getMock('HTML_QuickForm2_Element', array('getType', 
                                  'getValue', 'setValue', '__toString'));
        $mockEl->expects($this->exactly(2))->method('getValue')
               ->will($this->returnValue('foo'));

        HTML_QuickForm2_Factory::registerRule('regex-alpha', 'HTML_QuickForm2_Rule_Regex',
                                              null, '/^[a-zA-Z]+$/');
        $alpha = HTML_QuickForm2_Factory::createRule('regex-alpha', $mockEl, 'an error');
        $this->assertTrue($alpha->validate());

        HTML_QuickForm2_Factory::registerRule('regex-numeric', 'HTML_QuickForm2_Rule_Regex', 
                                              null, '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/');
        $numeric = HTML_QuickForm2_Factory::createRule('regex-numeric', $mockEl, 'an error');
        $this->assertFalse($numeric->validate());
    }

    public function testConfigOverridesOptions()
    {
        $mockEl  = $this->getMock('HTML_QuickForm2_Element', array('getType', 
                                  'getValue', 'setValue', '__toString'));
        $mockEl->expects($this->once())->method('getValue')
               ->will($this->returnValue('foo'));

        HTML_QuickForm2_Factory::registerRule('regex-override', 'HTML_QuickForm2_Rule_Regex',
                                              null, '/^[a-zA-Z]+$/');
        $override = HTML_QuickForm2_Factory::createRule('regex-override', $mockEl,
                                                        'an error', '/^[0-9]+$/');
        $this->assertTrue($override->validate());
    }

    public function testBug10799()
    {
        $mockInvalid = $this->getMock('HTML_QuickForm2_Element', array('getType', 
                                      'getValue', 'setValue', '__toString'));
        $mockInvalid->expects($this->once())->method('getValue')
                    ->will($this->returnValue("12345\n"));
        $ruleNumeric = new HTML_QuickForm2_Rule_Regex($mockInvalid, 'not valid',
                                                      '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/');
        $this->assertFalse($ruleNumeric->validate());
    }
}
?>