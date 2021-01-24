<?php
class Handler extends \Nyxt\Controller {
    public function handle() {
        $this->by_property = "hello";
        $this
            ->setByMethod("hello")
            ->setChainMethod("world");

        print_r($_SERVER);

        $this->render('index', ['by_arg' => 1]);
    }

    function validate($v): string|bool {
        $validation = $v->validate($_GET, [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validation->fails()) {
            $this->why = array_values($validation->errors()->firstOfAll())[0];

            return false;
        }

        return true;
    }
}
