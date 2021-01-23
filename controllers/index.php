<?php
class Handler extends Controller {
    public function handle() {
        $this->by_property = "hello";
        $this
            ->setByMethod("hello")
            ->setChainMethod("world");

        $this->render('index', ['by_arg' => 1]);
    }
}
