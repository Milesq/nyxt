<?php
class Handler extends \Nyxt\Controller {
    public function handle() {
        echo "Welcome {$_GET['name']}!".PHP_EOL;
        echo "We sent a mail to: {$_GET['email']}";
    }

    function validate($v): string|bool {
        // you can learn more about validation here: https://github.com/rakit/validation
        $validation = $v->validate($_GET, [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validation->fails()) {
            $this->why = array_values($validation->errors()->firstOfAll())[0];

            return false; // now [error].html will be rendered
        }

        return true;
    }
}
