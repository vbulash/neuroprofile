<?php


namespace App\Views\Composers;

use Illuminate\View\View;

class SessionIdComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        //$view->with('sid', session()->getId());
    }
}
