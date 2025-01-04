<?php 
require_once(__DIR__.'/../config/db.php');
class User extends Db {

public function __construct()
{
    parent::__construct();
}

public function register($user) {
   
    try {
        // Prepare and execute the insertion query
        $result = $this->conn->prepare("INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, email, role) VALUES (?, ?, ?, ?)");
        $result->execute($user);
        return $this->conn->lastInsertId();
        
       
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

public function login($userData){
    
    try {
        $result = $this->conn->prepare("SELECT * FROM utilisateurs WHERE email=?");
        $result->execute([$userData[0]]);
        $user = $result->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($userData[1], $user["mot_de_passe"])){
           

           return  $user ;
        
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

public function getStatistics() {
    $statistics = [];

    // Total number of users
    $query = $this->conn->prepare("SELECT COUNT(*) AS total_users FROM utilisateurs");
    $query->execute();
    $statistics['total_users'] = $query->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Total number of published projects
    $query = $this->conn->prepare("SELECT COUNT(*) AS total_projects FROM projets");
    $query->execute();
    $statistics['total_projects'] = $query->fetch(PDO::FETCH_ASSOC)['total_projects'];

    // Total number of freelancers
    $query = $this->conn->prepare("SELECT COUNT(*) AS total_freelancers FROM utilisateurs WHERE role = '3'");
    $query->execute();
    $statistics['total_freelancers'] = $query->fetch(PDO::FETCH_ASSOC)['total_freelancers'];

    // Number of ongoing offers (status = 2)
    $query = $this->conn->prepare("SELECT COUNT(*) AS ongoing_offers FROM offres WHERE status = 2");
    $query->execute();
    $statistics['ongoing_offers'] = $query->fetch(PDO::FETCH_ASSOC)['ongoing_offers'];

    return $statistics;
}

public function getAllUsers($filter, $userToSearch =''){


      
        $query = "SELECT * FROM utilisateurs WHERE role != 1"; // by default we show all users except admins
        
        // add filter to query
        if ($filter == 'clients') {
            $query .= " AND role = 2";
        } elseif ($filter == 'freelancers') {
            $query .= " AND role = 3";
        }
        
        // add search condition to query
        if ($userToSearch) {
            $query .= " AND nom_utilisateur LIKE ?";
        }
        
        $resul = $this->conn->prepare($query);
        $resul->execute($userToSearch ? ["%$userToSearch%"] : []);
        
        // Fetch and return results
        $users = $resul->fetchAll(PDO::FETCH_ASSOC);
        return $users;
   

}
// remove user 
public function removeUser($userId){
      $removeUser = $this -> conn ->prepare("DELETE FROM utilisateurs WHERE id_utilisateur=?");
        $removeUser->execute([$userId]);
}
        // function to block user
    function changeStatus($idUser){

        // get the old status
        $stmt = $this ->conn->prepare("SELECT is_active FROM utilisateurs WHERE id_utilisateur = ?");
        $stmt->execute([$idUser]);
        $currentStatus = $stmt->fetchColumn();

        $changeStatus = $this ->conn->prepare("UPDATE utilisateurs SET is_active=? WHERE id_utilisateur=?");
        $changeStatus->execute([$currentStatus==0?1:0,$idUser]);
    }
    // show caategories
    function getCategoriesWithSubcategories() {
            $query = $this -> conn->prepare("
                SELECT 
                    c.id_categorie,
                    c.nom_categorie,
                    sc.id_sous_categorie,
                    sc.nom_sous_categorie
                FROM 
                    categories c
                LEFT JOIN 
                    sous_categories sc ON c.id_categorie = sc.id_categorie
            ");
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
    
            return $results;
    }
    // add or midfy category
    function addModCat($category_id, $category_name){
        if (!empty($category_name)) {
            // create a new category if id not gived
            if($category_id==0){
                try {
                    $AddCategoryQuery = $this -> conn->prepare("INSERT INTO categories (nom_categorie) VALUES (:category_name)");
                    $AddCategoryQuery->execute([':category_name' => $category_name]);

                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }
            }
            // modify category if id gived
            else{
                try {
                    $modifyCategoryQuery = $this -> conn->prepare("UPDATE categories SET nom_categorie = ? WHERE id_categorie = ?");
                    $modifyCategoryQuery->execute([$category_name,$category_id]);

                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }
            }
            
        } 
    }
    // add or midfy Subcategory
    function addModSubCat($category_id, $subcategory_name,$subcategory_id){
        if (!empty($subcategory_name)) {
            // create a new subcategory if id not gived
            if($subcategory_id==0){
                try {
                    $AddSubCategoryQuery = $this -> conn->prepare("INSERT INTO sous_categories (nom_sous_categorie, id_categorie) VALUES (:subcategory_name, :category_id)");
                    $AddSubCategoryQuery->execute([':subcategory_name' => $subcategory_name,':category_id' => $category_id]);


                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }
            }
            // modify subcategory if id gived
            else{
                try {
                    $modifySubCategoryQuery = $this -> conn->prepare("UPDATE sous_categories SET nom_sous_categorie = ? WHERE id_sous_categorie = ?");
                    $modifySubCategoryQuery->execute([$subcategory_name,$subcategory_id]);

                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }
            }
            
        } 
    }
    // delete category
    function deleteCat($id_categorie){
        $deleteCategorieQuery=$this -> conn->prepare("DELETE FROM categories WHERE id_categorie=?");
        $deleteCategorieQuery->execute([$id_categorie]);
    }
    // delete Subcategory
    function deleteSubCat( $id_sous_categorie){
        $deleteSubCategorieQuery=$this -> conn->prepare("DELETE FROM sous_categories WHERE id_sous_categorie=?");
        $deleteSubCategorieQuery->execute([$id_sous_categorie]);
    }
    // show projects
    function showProjects($filter_by_cat, $filter_by_sub_cat,$filter_by_status, $projectToSearch = '') {
        $query = "SELECT p.id_projet, p.titre_projet, p.description,
                         p.id_categorie, p.id_sous_categorie, p.id_utilisateur,
                         p.project_status, c.nom_categorie AS nom_categorie,
                         sc.nom_sous_categorie AS nom_sous_categorie
                FROM projets p
                JOIN categories c ON c.id_categorie = p.id_categorie
                JOIN sous_categories sc ON sc.id_sous_categorie = p.id_sous_categorie
                WHERE 1=1";

        $params = [];
        // Add condition to show only client projects
        if (strstr($_SERVER['REQUEST_URI'], "Client")) {
            $query .= " AND p.id_utilisateur = :user_id";
            $params['user_id'] = $_SESSION['user_loged_in_id'];
        }       
    
        // Add filter by category if not 'all'
        if ($filter_by_cat !== 'all') {
            $query .= " AND c.nom_categorie = :filter_by_cat";
            $params['filter_by_cat'] = $filter_by_cat;
        }
    
        // Add filter by subcategory if not 'all'
        if ($filter_by_sub_cat !== 'all') {
            $query .= " AND sc.nom_sous_categorie = :filter_by_sub_cat";
            $params['filter_by_sub_cat'] = $filter_by_sub_cat;
        }

        // Add filter by status if not 'all'
        if (!empty($filter_by_status) && $filter_by_status !== 'all') {
            $query .= " AND p.project_status = :filter_by_status";
            $params['filter_by_status'] = $filter_by_status;
        }        

        // Add search condition if a search term is provided
        if ($projectToSearch) {
            $query .= " AND p.titre_projet LIKE :search_term";
            $params['search_term'] = "%$projectToSearch%";
        }
    
        // Prepare and execute the query
        $stmt = $this -> conn ->prepare($query);
        $stmt->execute($params);
    
        // Fetch and return results
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $projects;
    }
    // remove project
    function removeProject($idProject){
        $removeProject = $this -> conn->prepare("DELETE FROM projets WHERE id_projet=?");
        $removeProject->execute([$idProject]);
    }
    function getClientTestimonials( $role, $userId = null) {
        // Base query
        $queryStr = "SELECT p.titre_projet, t.commentaire, t.id_temoignage, o.montant, o.delai, o.id_offre
                    FROM temoignages t
                    JOIN offres o ON t.id_offre = o.id_offre
                    JOIN projets p ON o.id_projet = p.id_projet";

        // Modify query based on the role
        $params = [];
        if ($role === 'Freelancer') {
            $queryStr .= " WHERE o.id_utilisateur = ?";
            $params[] = $userId;
        } elseif ($role === 'Client') {
            $queryStr .= " WHERE p.id_utilisateur = ?";
            $params[] = $userId;
        }
        // Admin has no additional conditions, so no modification to the query

        // Prepare and execute the query
        $query = $this -> conn ->prepare($queryStr);
        $query->execute($params);

        // Fetch and return results
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    // REMOVE TESTIMONIALS
    function removeTestimonial($idtesTimonial){
        $removeTestimonial = $this -> conn ->prepare("DELETE FROM temoignages WHERE id_temoignage=?");
        $removeTestimonial->execute([$idtesTimonial]);
    }
    
}