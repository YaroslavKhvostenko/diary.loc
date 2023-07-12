<?php
class ViewModel
{
    /**
     * Form a correct view params
     * @param $opt
     * @return array
     */
    public function getPageOptions($opt)
    {
        $arr = [];
        $arr['title']   = isset($opt['title'])   ? $opt['title']   : 'Main';
        $arr['content'] = isset($opt['content']) ? $opt['content'] : 'main.phtml';
        $arr['header']  = isset($opt['header'])  ? $opt['header']  : 'header.phtml';
        return $arr;
    }

    /**
     * include a view template
     * @param $opt
     * @param $loggedUser
     * @param $session
     * @param null $data
     */
    public function render($opt, $loggedUser, $session, $data = null)
    {
        include_once "views/index.phtml";
    }
}