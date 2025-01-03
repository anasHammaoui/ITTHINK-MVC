<?php 
require_once (__DIR__.'/../models/User.php');

class AdminController extends BaseController {
    private $UserModel ;
    public function __construct(){

        $this->UserModel = new User();
  
        
     }

   public function index() {
      
      if(!isset($_SESSION['user_loged_in_id'])){
         header("Location: /login ");
         exit;
      }
     $statistics =  $this->UserModel->getStatistics();
    $this->renderDashboard('admin/index', ["statistics" => $statistics]);
   }
   
   public function categories() {

    $this->renderDashboard('admin/categories');
   }
   public function testimonials() {
 
    $this->renderDashboard('admin/testimonials');
   }
   public function projects() {
  
    $this->renderDashboard('admin/projects');
   }

   public function handleUsers(){
  


    
    // Get filter and search values from GET
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Default to 'all' if no filter is selected
    $userToSearch = isset($_GET['userToSearch']) ? $_GET['userToSearch'] : ''; // Default to empty if no search term is provided
    // var_dump($userToSearch);die();

    // Call showUsers with both filter and search term
    $users = $this->UserModel->getAllUsers($filter, $userToSearch);
    $this->renderDashboard('admin/users',["users"=> $users]);
   }

   //  function to remove user
    function removeUser(){
      //   include '../connection.php';
      
      if (isset($_GET["remove_user"])){
         $this ->  UserModel -> removeUser($_GET["remove_user"]);
         $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Default to 'all' if no filter is selected
          // Call showUsers with both filter and search term
          $users = $this->UserModel->getAllUsers($filter);
           $this->renderDashboard('admin/users',["users"=> $users]);
      }
    }

      function blockUser(){
             if (isset($_GET['block_user_id'])) {
        $idUser = $_GET['block_user_id'];
       $this -> UserModel -> changeStatus($idUser);
        // Redirect to avoid form resubmission after page reload
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Default to 'all' if no filter is selected
        // Call showUsers with both filter and search term
        $users = $this->UserModel->getAllUsers($filter);
        $this->renderDashboard('admin/users',["users"=> $users]);

    }
      }
   //  show categories
      function showCats(){
         $results = $this -> UserModel -> getCategoriesWithSubcategories();
         $categories = [];
            foreach ($results as $row) {
                $id_categorie = $row['id_categorie'];
    
                // Initialize category if not present
                if (!isset($categories[$id_categorie])) {
                    $categories[$id_categorie] = [
                        'id_categorie' => $id_categorie,
                        'nom_categorie' => $row['nom_categorie'],
                        'sous_categories' => []
                    ];
                }
    
                // Add subcategories
                if (!empty($row['id_sous_categorie'])) {
                    $categories[$id_categorie]['sous_categories'][] = [
                        'id_sous_categorie' => $row['id_sous_categorie'],
                        'nom_sous_categorie' => $row['nom_sous_categorie']
                    ];
                }
            }
            $this->renderDashboard('admin/categories',["categories"=> $categories]);
      }
      // projects function 
      function projectsMethod(){
         // Get filter and search values from GET
    $filter_by_cat = isset($_GET['filter_by_cat']) ? $_GET['filter_by_cat'] : 'all';
    $filter_by_sub_cat = isset($_GET['filter_by_sub_cat']) ? $_GET['filter_by_sub_cat'] : 'all';
    $projectToSearch = isset($_GET['projectToSearch']) ? $_GET['projectToSearch'] : '';
    $filter_by_status = isset($_GET['filter_by_status']) ? $_GET['filter_by_status'] : '';

    
    // Call showProjects with both filters and the search term
    $projects =$this -> UserModel -> showProjects($filter_by_cat, $filter_by_sub_cat,$filter_by_status, $projectToSearch) ;
    $this->renderDashboard('admin/projects',["projects"=> $projects]);
      }
      function removePro(){
         var_dump("get");
         if (isset($_GET['remove_project'])) {
            $idUser = $_GET['id_projet'];
            $this -> UserModel -> removeProject($idUser);
            // Redirect to avoid form resubmission after page reload
            $this->renderDashboard('admin/projects');
        }
      }
 

}