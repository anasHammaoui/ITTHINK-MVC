<?php
require_once (__DIR__.'/../models/User.php');
class ClientController extends BaseController{
    private $UserModel;
    public function __construct(){
        $this -> UserModel = new user();
    }
    function index(){
        $statistics =  $this->UserModel->getStatistics();
    $this->renderDashboard('/client/index', ["statistics" => $statistics]);
    }
    // projects
    function projects(){
         // Get filter and search values from GET
    $filter_by_cat = isset($_GET['filter_by_cat']) ? $_GET['filter_by_cat'] : 'all';
    $filter_by_sub_cat = isset($_GET['filter_by_sub_cat']) ? $_GET['filter_by_sub_cat'] : 'all';
    $projectToSearch = isset($_GET['projectToSearch']) ? $_GET['projectToSearch'] : '';
    $filter_by_status = isset($_GET['filter_by_status']) ? $_GET['filter_by_status'] : '';
       
    // Call showProjects with both filters and the search term
    $projects =$this -> UserModel -> showProjects($filter_by_cat, $filter_by_sub_cat,$filter_by_status, $projectToSearch);
    $this -> renderDashboard("/client/projects",["projects" =>$projects]);
    }
    // remove project
    function removePro(){
        if (isset($_GET['remove_project'])) {
           $idUser = $_GET['id_projet'];
           $this -> UserModel -> removeProject($idUser);
           // Redirect to avoid form resubmission after page reload
           $this->renderDashboard('/client/projects');
       }
     }
}
?>