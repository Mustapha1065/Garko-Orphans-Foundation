<?php
namespace GOF\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        // Redirect to admin if logged in, else to login
        if($this->currentUser()) {
            $this->redirect('/admin');
        } else {
            $this->redirect('/login');
        }
    }

    public function health(): void
    {
        $status = ['status'=>'ok','time'=>date('c'),'version'=>'1.0.0'];
        try {
            \GOF\Helpers\Database::get()->query('SELECT 1');
            $status['db'] = 'ok';
        } catch(\Throwable $e) {
            $status['db'] = 'error';
            $status['status'] = 'degraded';
        }
        $this->json($status);
    }
}
