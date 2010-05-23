<?php
/* {{{
Copyright (c) 2010 Martin Králik

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

require_once 'libfajr/AIS2Table.php';

class TableRow
{
	private $data = null;
	private $options = null;
	
	public function __construct($table, $rowData, $options = null) {
		assert(isset($rowData['index'])); // row should have index
		$this->data = $rowData;
		$this->options = $options;
		$this->table = $table;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getHtml() {
		$table = $this->table;
		$columns = $table->getColumns();
		$class='';
		if ($table->GetOption('selected_key') == $this->data['index']) {
			$class = 'selected';
		} else if (isset($this->options['class'])) {
			$class=$this->options['class'];
		}
		
		$row = "<tr class='$class'>";
		
		if ($table->newKey) {
			$link = '?'.http_build_query(array_merge($table->urlParams,
						array($table->newKey => $this->data['index'])));
		}
		
		$colno = 0;
		foreach ($columns as $key => $column)
		{
			$cell_value = $this->data[substr($key, 0, 32)];
			if ($cell_value=="") $cell_value="&nbsp;";
				
			$cell = '    <td>';
			if ($table->newKey && $colno==0) {
				$cell .= '<a href="'.hescape($link).'">'.$cell_value."</a>";
			} else {
				$cell .= $cell_value;
			}
			$cell .= "</td>\n";
			
			$row .= $cell;
			$colno++;
		}
		$row .= "</tr>\n";
		
		return $row;
	}
	
}


/**
 * Trieda na vygenerovanie tabulky z jej definície a vstupného HTML.
 *
 * @author majak
 */
class Table
{
	protected $definition = null;
	protected $data = array();
	protected $name = null;
	public $newKey = null;
	public $urlParams = array();
	protected $options = null;

/**
 * Konštruktor.
 * Nastaví atribúty a zo vstupného HTML dá tabuľku do poľa.
 * @param array $definition Definícia tabuľky.
 * @param string $html HTML z AISu.
 * @param string $name Názov tabuľky.
 * @param string|null $newKey Názov nového parametru v url, ktorého hodnota bude závisieť od riadku tabuľky.
 * @param array $urlParams Zvyšné už nastavené parametre pre url.
 */
	public function  __construct($definition, $name = '', $newKey = null, $urlParams = array())
	{
		$this->definition = $definition;
		$this->name = $name;
		$this->newKey = $newKey;
		$this->urlParams = $urlParams;
	}
	
	public function addRows($data) {
		foreach ($data as $row) {
				$this->addRow($row, null);
		}
	}
	
	public function addRow($rowData, $rowOptions) {
		$this->data[] = new TableRow($this, $rowData, $rowOptions);
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setNewKey($newKey)
	{
		$this->newKey = $newKey;
	}

	public function setUrlParams($urlParams)
	{
		$this->urlParams = $urlParams;
	}
	
	public function setOptions($options)
	{
		$this->options = $options;
	}
	
	public function setOption($name, $value) {
		$this->options[$name] = $value;	
	}
	
	public function getOption($name) {
		if (isset($this->options[$name])) {
			return $this->options[$name];
		}
		return null;
	}
	
	public function getColumns() {
		$data=array();
		foreach ($this->definition as $key => $value) {
			if (!$value['visible']) continue; // skip invisible cells
			$colNum = 0;
			if (isset($value['col'])) $colNum=$value['col'];
			$data[] = array($colNum, $key);
		}
		sort($data);
		$columns=array();
		foreach ($data as $row) {
			$columns[$row[1]] = $this->definition[$row[1]];
		}
		return $columns;
	}

	/**
	 * Pomocou nastavených atribútov vygeneruje tabuľku v HTML formáte aj s linkami.
	 * @return string Vygenerovaná tabuľka v HTML formáte.
	 */
	public function getHtml()
	{
		$id = rand(); // FIXME: rozumny id generator
		
		$table = "\n<!-- ******* Table:{$this->name} ****** -->\n";
		$table .= "<div class='table'>\n";
		if ($this->name) $table .= '<h2> <img class=\'togglevisibilityimg\' id=\'toggle'.$id.'\'
			src=\'images/minimize.gif\' onClick=\'toggleVisibility('.$id.');\'> '.$this->name.'</h2>'."\n";
		if (!is_array($this->data) || empty($this->data[0])) {
			$table .= '<font color="red"> Dáta pre túto tabuľku neboli nájdené.</font><hr class="space" />';
			$table .= "</div>\n";
			return $table;
		}
		
		$table .= '<table id=\''.$id."'class='colstyle-sorting'>\n<thead>\n<tr>\n";
		$columns = $this->getColumns();
		
		foreach ($columns as $key=>$value) {
			$table .= '    <th class="sortable">'.$value['title']."</th>\n";
		}
		
		$table .= "</tr>\n";
		$table .= "\n</thead>\n<tbody>\n";
		foreach ($this->data as $row)
		{
			$table .= $row->getHtml();
		}
	$table .= "</tbody></table>\n";
	if ($this->getOption('collapsed')) {
		$table .= '<script type="text/javascript"> toggleVisibility('.
							$id.") </script>\n";
	}
	$table .= "</div>\n\n\n";
	return $table;
	}

}
?>
