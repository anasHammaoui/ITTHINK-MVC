<?php
require_once (__DIR__.'/../models/User.php');
class ClientController extends BaseController{
    private $UserModel;
    public function __construct(){
        $this -> UserModel = new user();
    }
    function index(){
        $statistics =  $this->UserModel->getStatistics();
    $this->renderDashboard('client/index', ["statistics" => $statistics]);
    }
}
?>