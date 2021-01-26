<?php
#[nyxt('orm')]
class Handler extends \Nyxt\Controller {
    function handle() {
        $this->bikes->docked()->where('id', '>', 3)->get();
    }
}
