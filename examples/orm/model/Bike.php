<?php
class Bike extends \Nyxt\Model {
    var array $__columns = ['position', 'docked'];

    public function docked() {
        return $this->findByDocked(true);
    }
}
