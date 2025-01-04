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
    $resultsCats = $this -> UserModel -> getCategoriesWithSubcategories();
    $this -> renderDashboard("/client/projects",["projects" =>$projects, "categories" => $resultsCats]);
    }
    // remove project
    function removePro(){
        if (isset($_GET['remove_project'])) {
           $idUser = $_GET['id_projet'];
           $this -> UserModel -> removeProject($idUser);
           // Redirect to avoid form resubmission after page reload
           $this->projects();
       }
     }
    //  add project
    function addModPro(){
        if (isset($_GET["save_project"])) {
            $project_title = trim($_GET["project_title_input"]);
            $project_description = trim($_GET["project_description_input"]);
            $project_category = $_GET["project_category_input"];
            $project_subcategory = $_GET["project_subcategory_input"];
            $project_id = isset($_GET["project_id_input"]) ? trim($_GET["project_id_input"]) : 0;
            $project_status=(int)$_GET["project_status_input"];

            // Check if required fields are not empty
            if (!empty($project_title) && !empty($project_description) && !empty($project_category) && !empty($project_subcategory)) {
                // Add new project if no ID provided
                if ($project_id == 0) {
                    $this -> UserModel -> addPro($project_title,$project_description,$project_category,$project_subcategory);
                }
                // Modify existing project if ID is provided
                else {
                    $this -> UserModel -> modifyPro($project_title,$project_description,$project_category,$project_subcategory,$project_status,$project_id);
                    
                }
                $this->projects();
            } else {
                echo "Please fill in all fields.";
            }
        }
    }
    // client offer page
    function clientOffer(){
        $id_offre_having_testimonial =$this -> UserModel -> getClientTestimonialsIds();
    $client_offers = $this -> UserModel -> getClientOffres();
    $this -> renderDashboard("client/offers",["id_offre_having_testimonial" => $id_offre_having_testimonial, "client_offers" => $client_offers]);
    }
    // accept offer
    function acceptOffer(){
        if (isset($_GET['accept_offre'])) {
            $idOffre = (int)$_GET['id_offre'];
            $this -> UserModel -> acceptOffre($idOffre);
            // Redirect to avoid form resubmission after page reload
           $this -> clientOffer();
        }
    }
}
?>