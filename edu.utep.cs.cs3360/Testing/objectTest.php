<?php
class Cart {
  public $items;
  function addItem($item, $num) {
    $this->items[$item] = $num;
  }
}

class NamedCart extends Cart {
	public $name;
	function NamedCart($name) {
		$this->name = $name;
	}
	function setName($name) {
		$this->name = $name;
	}
	function getName() {
		return $this->name;
	}
}

$cart1 = new NamedCart("Cathy");
$cart2 = $cart1;
$cart1->setName("Judy");
$cart1 = null;
echo "Test1 " . $cart2->getName() . "\n"; // output?
/* // Commented out because it will give you an error when referring to $cart2->getName();
 * // $cart2 is null.
$cart1 = new NamedCart("Cathy");
$cart2 =& $cart1;
$cart1->setName("Judy");
$cart1 = null;
echo "Test2 " . $cart2->getName(); // output?
*/
$cart1 = new NamedCart("Cathy");
$cart2 = clone $cart1;
$cart1->setName("Judy");
$cart1 = null;
echo "Test3 " . $cart2->getName(); // output?

?>
