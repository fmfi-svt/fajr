<?php
/* {{{
Copyright (c) 2010 Martin Králik
Copyright (c) 2010 Martin Sucha

 Permission is hereby granted, free of charge, to any person
 obtaining a copy of this software and associated documentation
 files (the "Software"), to deal in the Software without
 restriction, including without limitation the rights to use,
 copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the
 Software is furnished to do so, subject to the following
 conditions:

 The above copyright notice and this permission notice shall be
 included in all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 OTHER DEALINGS IN THE SOFTWARE.
 }}} */

require_once 'DisplayManager.php';
require_once 'Renderable.php';

/**
 * Trieda, ktora vygeneruje okolo daneho Renderable taky HTML kod, aby
 * ho bolo mozne javascriptom schovat
 */
class Collapsible implements Renderable {

	protected $name = null;
	protected $content = null;
	protected $collapsed = false;

	/**
	 * Konstruktor
	 * @param string $title Titulok schovavanej oblasti
	 * @param Renderable $content Obsah schovavanej oblasti
	 */
	function __construct($title, Renderable $content, $collapsed = false) {
		assert($content != null);
		$this->setTitle($title);
		$this->setContent($content);
		$this->setCollapsed($collapsed);
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		assert($title != null);
		$this->title = $title;
	}

	public function getCollapsed() {
	 return $this->collapsed;
	}

	public function setCollapsed($collapsed) {
	 $this->collapsed = $collapsed;
	}

	public function getContent() {
	 return $this->content;
	}

	public function setContent($content) {
	 $this->content = $content;
	}

	public function getHtml() {
		$id = DisplayManager::getUniqueHTMLId('collapsible');

		$html = '<div class="collapsible" id="'.$id.'"'."\n";
		$html .= '<h2 class="togglevisibility" onclick=\'toggleVisibility("'.$id.'");\' >';
		$html .= $this->title.'</h2>'."\n";
		$html .= '<div class="collapsiblecontent">';
		$html .= $this->content->getHtml();
		$html .= '</div>';
		$html .= "</div>\n\n\n";
		if ($this->collapsed) {
			$html .= '<script type="text/javascript"> toggleVisibility("';
			$html .= $id."\") </script>\n";
		}

		return $html;
	}



}