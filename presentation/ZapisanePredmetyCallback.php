<?php
class ZapisanePredmetyCallback implements Renderable {
	private $skusky;
	
	public function __construct($skusky) {
		$this->skusky = $skusky;
	}
	
	public function getHtml() {
		$predmetyZapisnehoListu = $this->skusky->getPredmetyZapisnehoListu();
		$predmetyZapisnehoListuTable = new
			Table(TableDefinitions::predmetyZapisnehoListu());
		$predmetyZapisnehoListuCollapsible = new Collapsible('Predmety zápisného listu',
			$predmetyZapisnehoListuTable);
		$kreditovCelkom = 0;
		foreach (Sorter::sort($predmetyZapisnehoListu->getData(),
					array("semester"=>-1, "nazov"=>1)) as $row) {
			if ($row['semester']=='L') $class='leto'; else $class='zima';
			$predmetyZapisnehoListuTable->addRow($row, array('class'=>$class));
			$kreditovCelkom += $row['kredit'];
		}
;
		$predmetyZapisnehoListuTable->addFooter(array('nazov'=>'Celkom','kredit'=>$kreditovCelkom), array());
		$predmetyZapisnehoListuTable->setUrlParams(array('studium' =>
					Input::get('studium'), 'list' => Input::get('list')));
		
		return $predmetyZapisnehoListuTable->getHtml();
	}
}