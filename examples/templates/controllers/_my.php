<?php
class Handler extends \Nyxt\Controller {
    public function handle() {
        // You can declare template arguments like:
        $this->by_property = "hello";
        $this->reset();
        $this
            ->setByMethod("hello")
            ->setChainMethod("world")
            ->unset('chainMethod');

        $this->render('index', ['by_arg' => 1]);
    }
}
